import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login, CookieJar, BASE_URL, ADMIN_EMAIL } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Register: Berhasil mendaftar dengan data valid", async () => {
  // Ambil CSRF token dulu
  const jar = new CookieJar();
  const csrfRes = await fetch(`${BASE_URL}/register`, { redirect: "manual" });
  jar.addFromResponse(csrfRes);

  const response = await request("/register", {
    method: "POST",
    jar,
    body: JSON.stringify({
      name: "Test User",
      email: "test@example.com",
      password: "Password123!",
      password_confirmation: "Password123!",
    }),
  });

  expect(response.status).toBe(302);
});

test("Register: Gagal mendaftar karena email sudah terdaftar", async () => {
  // Register pertama
  const jar1 = new CookieJar();
  const csrf1 = await fetch(`${BASE_URL}/register`, { redirect: "manual" });
  jar1.addFromResponse(csrf1);

  const firstResponse = await request("/register", {
    method: "POST",
    jar: jar1,
    body: JSON.stringify({
      name: "User 1",
      email: "duplicate@example.com",
      password: "Password123!",
      password_confirmation: "Password123!",
    }),
  });

  expect(firstResponse.status).toBe(302);

  // Register kedua dengan email yang sama
  const jar2 = new CookieJar();
  const csrf2 = await fetch(`${BASE_URL}/register`, { redirect: "manual" });
  jar2.addFromResponse(csrf2);

  const response = await request("/register", {
    method: "POST",
    jar: jar2,
    body: JSON.stringify({
      name: "User 2",
      email: "duplicate@example.com",
      password: "Password123!",
      password_confirmation: "Password123!",
    }),
  });

  expect(response.status).toBe(422);
});

test("Login: Berhasil login dengan kredensial benar", async () => {
  // Register user dulu
  const regJar = new CookieJar();
  const csrfReg = await fetch(`${BASE_URL}/register`, { redirect: "manual" });
  regJar.addFromResponse(csrfReg);

  const registerResponse = await request("/register", {
    method: "POST",
    jar: regJar,
    body: JSON.stringify({
      name: "Login User",
      email: "login@example.com",
      password: "Password123!",
      password_confirmation: "Password123!",
    }),
  });

  expect(registerResponse.status).toBe(302);

  // Login
  const jar = new CookieJar();
  const csrfLogin = await fetch(`${BASE_URL}/login`, { redirect: "manual" });
  jar.addFromResponse(csrfLogin);

  const response = await request("/login", {
    method: "POST",
    jar,
    body: JSON.stringify({
      email: "login@example.com",
      password: "Password123!",
    }),
  });

  expect(response.status).toBe(302);
});

test("Login: Gagal login dengan password salah", async () => {
  const jar = new CookieJar();
  const csrfRes = await fetch(`${BASE_URL}/login`, { redirect: "manual" });
  jar.addFromResponse(csrfRes);

  const response = await request("/login", {
    method: "POST",
    jar,
    body: JSON.stringify({
      email: ADMIN_EMAIL,
      password: "wrongpassword",
    }),
  });

  expect(response.status).toBe(422);
});

test("Logout: Berhasil logout", async () => {
  const jar = await login();

  const response = await request("/logout", {
    method: "POST",
    jar,
  });

  expect(response.status).toBe(302);
});

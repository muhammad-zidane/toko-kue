import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Register: Berhasil mendaftar dengan data valid", async () => {
  const response = await request("/register", {
    method: "POST",
    body: JSON.stringify({
      name: "Test User",
      email: "test@example.com",
      password: "password123",
      password_confirmation: "password123",
    }),
  });

  expect(response.status).toBe(200);
});

test("Register: Gagal mendaftar karena email sudah terdaftar", async () => {
  await request("/register", {
    method: "POST",
    body: JSON.stringify({
      name: "User 1",
      email: "duplicate@example.com",
      password: "password",
      password_confirmation: "password",
    }),
  });

  const response = await request("/register", {
    method: "POST",
    body: JSON.stringify({
      name: "User 2",
      email: "duplicate@example.com",
      password: "password",
      password_confirmation: "password",
    }),
  });

  expect(response.status).toBe(422);
});

test("Login: Berhasil login dengan kredensial benar", async () => {
  await request("/register", {
    method: "POST",
    body: JSON.stringify({
      name: "Login User",
      email: "login@example.com",
      password: "password",
      password_confirmation: "password",
    }),
  });

  const response = await request("/login", {
    method: "POST",
    body: JSON.stringify({
      email: "login@example.com",
      password: "password",
    }),
  });

  expect(response.status).toBe(200);
});

test("Login: Gagal login dengan password salah", async () => {
  await request("/register", {
    method: "POST",
    body: JSON.stringify({
      name: "Wrong Pass User",
      email: "wrong@example.com",
      password: "password",
      password_confirmation: "password",
    }),
  });

  const response = await request("/login", {
    method: "POST",
    body: JSON.stringify({
      email: "wrong@example.com",
      password: "wrongpassword",
    }),
  });

  expect(response.status).toBe(422);
});

test("Logout: Berhasil logout", async () => {
  const loginRes = await request("/login", {
    method: "POST",
    body: JSON.stringify({
      email: "admin@tokokue.com",
      password: "password",
    }),
  });

  const cookie = loginRes.headers.get("set-cookie");

  const response = await request("/logout", {
    method: "POST",
    headers: {
      Cookie: cookie || "",
    },
  });

  expect(response.status).toBe(200);
});

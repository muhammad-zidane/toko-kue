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

  // Laravel redirect after register
  expect(response.status).toBe(200); // Or 302 if followed, but request helper doesn't follow by default
});

test("Register: Gagal mendaftar karena email sudah terdaftar", async () => {
  // First registration
  await request("/register", {
    method: "POST",
    body: JSON.stringify({
      name: "User 1",
      email: "duplicate@example.com",
      password: "password",
      password_confirmation: "password",
    }),
  });

  // Second registration with same email
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
  const data = await response.json();
  expect(data.errors.email).toBeDefined();
});

test("Login: Berhasil login dengan kredensial benar", async () => {
  // Register first
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
  // Login first
  const loginRes = await request("/login", {
    method: "POST",
    body: JSON.stringify({
      email: "admin@example.com", // Assume seeded
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

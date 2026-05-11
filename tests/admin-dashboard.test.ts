import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login, CookieJar, BASE_URL } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin: Customer tidak bisa akses dashboard admin", async () => {
  // Login sebagai admin dulu, lalu buat customer via seeder (customer sudah ada)
  // Kita langsung login sebagai user yang bukan admin
  // Register customer baru lewat helper
  const jar = new CookieJar();
  const csrfRes = await fetch(`${BASE_URL}/register`, { redirect: "manual" });
  jar.addFromResponse(csrfRes);

  await request("/register", {
    method: "POST",
    jar,
    body: JSON.stringify({
      name: "Customer",
      email: "customer@example.com",
      password: "password",
      password_confirmation: "password",
    }),
  });

  // Login sebagai customer
  const customerJar = await login("customer@example.com", "password");
  const response = await request("/admin/dashboard", { jar: customerJar });
  expect(response.status).toBe(403);
}, 15000); // Timeout 15 detik karena banyak request

test("Admin: Admin berhasil akses dashboard", async () => {
  const jar = await login();
  const response = await request("/admin/dashboard", { jar });
  expect(response.status).toBe(200);
}, 15000);

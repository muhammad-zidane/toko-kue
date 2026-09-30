import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login, CUSTOMER_EMAIL } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin: Customer tidak bisa akses dashboard admin", async () => {
  const customerJar = await login(CUSTOMER_EMAIL, "password");
  const response = await request("/admin/dashboard", { jar: customerJar });
  expect(response.status).toBe(403);
}, 15000); // Timeout 15 detik karena banyak request

test("Admin: Admin berhasil akses dashboard", async () => {
  const jar = await login();
  const response = await request("/admin/dashboard", { jar });
  expect(response.status).toBe(200);
}, 15000);

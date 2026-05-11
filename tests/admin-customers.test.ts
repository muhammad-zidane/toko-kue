import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Customers: Berhasil melihat daftar pelanggan", async () => {
  const jar = await login();
  const response = await request("/admin/customers", { jar });
  expect(response.status).toBe(200);
});

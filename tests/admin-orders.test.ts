import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Orders: Berhasil melihat daftar pesanan", async () => {
  const jar = await login();
  const response = await request("/admin/orders", { jar });
  expect(response.status).toBe(200);
});

test("Admin Orders: Berhasil update status pesanan ke processing", async () => {
  const jar = await login();

  // DemoSeeder sudah menyediakan pesanan dengan ID 1.
  const response = await request("/admin/orders/1/status/processing", {
    method: "PATCH",
    jar,
  });

  expect(response.status).toBe(302);
});

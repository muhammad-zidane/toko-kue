import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Orders: Berhasil melihat daftar pesanan", async () => {
  const cookie = await login();
  const response = await request("/admin/orders", {
    headers: { Cookie: cookie || "" },
  });
  expect(response.status).toBe(200);
});

test("Admin Orders: Berhasil update status pesanan", async () => {
  const cookie = await login();
  
  // Update status of order 1 to processing
  const response = await request("/admin/orders/1/status/processing", {
    method: "PATCH",
    headers: { Cookie: cookie || "" },
  });

  expect(response.status).toBe(200);
});

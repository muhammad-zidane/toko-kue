import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Finance: Berhasil melihat halaman keuangan", async () => {
  const jar = await login();
  const response = await request("/admin/finance", { jar });
  expect(response.status).toBe(200);
});

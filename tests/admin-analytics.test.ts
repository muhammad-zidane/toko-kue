import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Analytics: Berhasil melihat halaman analisis", async () => {
  const jar = await login();
  const response = await request("/admin/analytics", { jar });
  expect(response.status).toBe(200);
});

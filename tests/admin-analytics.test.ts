import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Analytics: Berhasil melihat halaman analisis", async () => {
  const cookie = await login();
  const response = await request("/admin/analytics", {
    headers: { Cookie: cookie || "" },
  });
  expect(response.status).toBe(200);
});

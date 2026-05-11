import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Finance: Berhasil melihat halaman keuangan", async () => {
  const cookie = await login();
  const response = await request("/admin/finance", {
    headers: { Cookie: cookie || "" },
  });
  expect(response.status).toBe(200);
});

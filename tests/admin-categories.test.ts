import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Categories: Berhasil menambah kategori", async () => {
  const cookie = await login();
  const response = await request("/admin/categories", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({
      name: "Kategori Baru",
      description: "Deskripsi kategori",
    }),
  });

  expect(response.status).toBe(200);
});

test("Admin Categories: Berhasil hapus kategori", async () => {
  const cookie = await login();
  const response = await request("/admin/categories/1", {
    method: "DELETE",
    headers: { Cookie: cookie || "" },
  });

  expect(response.status).toBe(200);
});

import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Categories: Berhasil menambah kategori", async () => {
  const jar = await login();
  const response = await request("/admin/categories", {
    method: "POST",
    jar,
    body: JSON.stringify({
      name: "Kategori Baru",
      description: "Deskripsi kategori",
    }),
  });
  expect(response.status).toBe(302);
});

test("Admin Categories: Berhasil hapus kategori", async () => {
  const jar = await login();
  const response = await request("/admin/categories/1", {
    method: "DELETE",
    jar,
  });
  expect(response.status).toBe(302);
});

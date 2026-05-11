import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Products: Berhasil menambah produk baru", async () => {
  const cookie = await login();
  const response = await request("/admin/products", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({
      name: "Produk Baru",
      category_id: 1,
      description: "Deskripsi",
      price: 15000,
      stock: 10,
    }),
  });

  expect(response.status).toBe(200);
});

test("Admin Products: Berhasil hapus produk", async () => {
  const cookie = await login();
  const response = await request("/admin/products/1", {
    method: "DELETE",
    headers: { Cookie: cookie || "" },
  });

  expect(response.status).toBe(200);
});

import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Products: Berhasil menambah produk baru", async () => {
  const jar = await login();
  const response = await request("/admin/products", {
    method: "POST",
    jar,
    body: JSON.stringify({
      name: "Produk Baru",
      category_id: 1,
      description: "Deskripsi",
      price: 15000,
      stock: 10,
    }),
  });
  expect(response.status).toBe(302);
});

test("Admin Products: Berhasil hapus produk", async () => {
  const jar = await login();
  const response = await request("/admin/products/kue-ulang-tahun-coklat", {
    method: "DELETE",
    jar,
  });
  expect(response.status).toBe(302);
});

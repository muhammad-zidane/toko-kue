import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Home Page: Berhasil memuat halaman utama", async () => {
  const response = await request("/");
  expect(response.status).toBe(200);
});

test("Products Index: Berhasil memuat daftar produk", async () => {
  const response = await request("/products");
  expect(response.status).toBe(200);
});

test("Product Detail: Berhasil memuat detail produk", async () => {
  const response = await request("/products/kue-ulang-tahun-coklat");
  expect(response.status).toBe(200);
});

test("Product Detail: Mengembalikan 404 jika produk tidak ditemukan", async () => {
  const response = await request("/products/produk-gaib");
  expect(response.status).toBe(404);
});

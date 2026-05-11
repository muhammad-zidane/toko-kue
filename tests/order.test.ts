import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Order: Berhasil melakukan checkout", async () => {
  const jar = await login();

  const response = await request("/orders", {
    method: "POST",
    jar,
    body: JSON.stringify({
      shipping_address: "Jl. Test No. 123",
      notes: "Cepat ya",
      items: [{ product_id: 1, quantity: 1 }],
      payment_method: "transfer",
    }),
  });

  expect(response.status).toBe(302);
});

test("Order: Berhasil melihat riwayat pesanan", async () => {
  const jar = await login();
  const response = await request("/orders", {
    jar,
    headers: { Accept: "text/html" },
  });
  // Halaman orders mengembalikan view, bukan JSON
  expect([200, 302]).toContain(response.status);
});

test("Order: Gagal checkout jika stok kurang", async () => {
  const jar = await login();

  const response = await request("/orders", {
    method: "POST",
    jar,
    body: JSON.stringify({
      shipping_address: "Alamat",
      items: [{ product_id: 1, quantity: 9999 }],
    }),
  });

  expect(response.status).toBe(302);
});

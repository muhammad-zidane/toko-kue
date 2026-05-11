import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Order: Berhasil melakukan checkout", async () => {
  const cookie = await login();

  // Add to cart first
  await request("/cart/add", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({ product_id: 1, quantity: 1 }),
  });

  // Store order
  const response = await request("/orders", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({
      shipping_address: "Jl. Test No. 123",
      notes: "Cepat ya",
      items: [
        { product_id: 1, quantity: 1 }
      ],
      payment_method: "transfer",
    }),
  });

  expect(response.status).toBe(200);
});

test("Order: Berhasil melihat riwayat pesanan", async () => {
  const cookie = await login();
  const response = await request("/orders", {
    headers: { Cookie: cookie || "" },
  });
  expect(response.status).toBe(200);
});

test("Order: Gagal checkout jika stok kurang", async () => {
  const cookie = await login();

  const response = await request("/orders", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({
      shipping_address: "Alamat",
      items: [
        { product_id: 1, quantity: 9999 } // Excessive quantity
      ],
    }),
  });

  expect(response.status).toBe(422);
});

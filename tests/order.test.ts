import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

function checkoutData(quantity: number) {
  const deliveryDate = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000)
    .toISOString()
    .slice(0, 10);

  return {
    delivery_method: "pickup",
    delivery_date: deliveryDate,
    delivery_slot: "08:00-11:00",
    items: [{ product_id: 1, quantity }],
    payment_method: "transfer_bank",
  };
}

test("Order: Berhasil melakukan checkout", async () => {
  const jar = await login();

  const response = await request("/orders", {
    method: "POST",
    jar,
    body: JSON.stringify({
      ...checkoutData(1),
      notes: "Cepat ya",
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
      ...checkoutData(9999),
    }),
  });

  expect(response.status).toBe(422);
  expect((await response.json()).errors).toHaveProperty("stock");
});

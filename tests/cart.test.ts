import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Cart: Berhasil menambah produk ke keranjang", async () => {
  const jar = await login();

  const addRes = await request("/cart/add", {
    method: "POST",
    jar,
    body: JSON.stringify({
      product_id: 1,
      quantity: 2,
    }),
  });

  expect(addRes.status).toBe(302);

  const cartRes = await request("/cart", { jar });
  expect(cartRes.status).toBe(200);
});

test("Cart: Berhasil mengosongkan keranjang", async () => {
  const jar = await login();

  await request("/cart/add", {
    method: "POST",
    jar,
    body: JSON.stringify({ product_id: 1, quantity: 1 }),
  });

  const clearRes = await request("/cart/clear", {
    method: "POST",
    jar,
  });
  expect(clearRes.status).toBe(302);
});

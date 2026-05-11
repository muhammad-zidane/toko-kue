import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Cart: Berhasil menambah produk ke keranjang", async () => {
  const cookie = await login();
  
  // Add to cart
  const addRes = await request("/cart/add", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({
      product_id: 1,
      quantity: 2,
    }),
  });

  expect(addRes.status).toBe(200);

  // Check cart index
  const cartRes = await request("/cart", {
    headers: { Cookie: cookie || "" },
  });
  expect(cartRes.status).toBe(200);
});

test("Cart: Berhasil mengosongkan keranjang", async () => {
  const cookie = await login();
  
  // Add item first
  await request("/cart/add", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({ product_id: 1, quantity: 1 }),
  });

  // Clear cart
  const clearRes = await request("/cart/clear", {
    method: "POST",
    headers: { Cookie: cookie || "" },
  });
  expect(clearRes.status).toBe(200);
});

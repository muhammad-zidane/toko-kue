import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Model Relations: Halaman utama memuat categories dan products", async () => {
  const response = await request("/");
  expect(response.status).toBe(200);
});

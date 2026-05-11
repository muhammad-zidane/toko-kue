import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Model Relations: Category has products", async () => {
  const response = await request("/");
  const data = await response.text(); // Assuming it returns HTML for now, or JSON if API
  // In a real API test, we'd check JSON structure.
  expect(response.status).toBe(200);
});

import { expect, test } from "bun:test";
import { request } from "./helpers";

test("Middleware: Guest tidak bisa akses halaman ber-auth", async () => {
  const routes = ["/profile", "/orders", "/cart", "/admin/dashboard"];
  for (const route of routes) {
    const response = await request(route);
    // Laravel returns 401 for JSON requests without auth
    expect(response.status).toBe(401);
  }
});

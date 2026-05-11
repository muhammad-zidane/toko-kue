import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin: Customer tidak bisa akses dashboard admin", async () => {
  // Register a regular customer
  await request("/register", {
    method: "POST",
    body: JSON.stringify({
      name: "Customer",
      email: "customer@example.com",
      password: "password",
      password_confirmation: "password",
    }),
  });

  const cookie = await login("customer@example.com", "password");
  
  const response = await request("/admin/dashboard", {
    headers: { Cookie: cookie || "" },
    redirect: "manual",
  });

  expect(response.status).toBe(403);
});

test("Admin: Admin berhasil akses dashboard", async () => {
  const cookie = await login(); // Default is admin@example.com
  const response = await request("/admin/dashboard", {
    headers: { Cookie: cookie || "" },
  });
  expect(response.status).toBe(200);
});

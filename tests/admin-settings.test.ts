import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Settings: Berhasil update profil admin", async () => {
  const cookie = await login();
  const response = await request("/admin/settings", {
    method: "POST",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({
      name: "Admin Baru",
      email: "admin@example.com",
    }),
  });

  expect(response.status).toBe(200);
});

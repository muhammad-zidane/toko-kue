import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Admin Settings: Berhasil update profil admin", async () => {
  const jar = await login();
  const response = await request("/admin/settings", {
    method: "POST",
    jar,
    body: JSON.stringify({
      name: "Admin Baru",
      email: "admin@tokokue.com",
    }),
  });
  expect(response.status).toBe(302);
});

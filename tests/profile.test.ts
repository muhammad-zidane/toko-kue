import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Profile: Harus 401 jika belum login", async () => {
  const response = await request("/profile");
  expect(response.status).toBe(401);
});

test("Profile: Berhasil melihat halaman profil sendiri", async () => {
  const jar = await login();
  const response = await request("/profile", { jar });
  expect(response.status).toBe(200);
});

test("Profile: Berhasil update profil", async () => {
  const jar = await login();
  const response = await request("/profile", {
    method: "PATCH",
    jar,
    body: JSON.stringify({
      name: "Updated Name",
      email: "admin@tokokue.com",
    }),
  });

  expect(response.status).toBe(302);
});

test("Profile: Gagal hapus akun dengan password salah", async () => {
  const jar = await login();
  const response = await request("/profile", {
    method: "DELETE",
    jar,
    body: JSON.stringify({
      password: "wrongpassword",
    }),
  });

  expect(response.status).toBe(422);
});

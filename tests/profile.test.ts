import { expect, test, beforeEach } from "bun:test";
import { request, resetDatabase, login } from "./helpers";

beforeEach(() => {
  resetDatabase();
});

test("Profile: Harus redirect jika belum login", async () => {
  const response = await request("/profile", { redirect: "manual" });
  expect(response.status).toBe(302);
});

test("Profile: Berhasil melihat halaman profil sendiri", async () => {
  const cookie = await login(); // Login as admin by default
  const response = await request("/profile", {
    headers: { Cookie: cookie || "" },
  });
  expect(response.status).toBe(200);
});

test("Profile: Berhasil update profil", async () => {
  const cookie = await login();
  const response = await request("/profile", {
    method: "PATCH",
    headers: { 
      Cookie: cookie || "",
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      name: "Updated Name",
      email: "admin@example.com",
    }),
  });

  expect(response.status).toBe(200);
});

test("Profile: Gagal hapus akun dengan password salah", async () => {
  const cookie = await login();
  const response = await request("/profile", {
    method: "DELETE",
    headers: { Cookie: cookie || "" },
    body: JSON.stringify({
      password: "wrongpassword",
    }),
  });

  expect(response.status).toBe(422);
});

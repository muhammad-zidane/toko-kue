import { execSync } from "child_process";

export const BASE_URL = "http://localhost:8000";

/**
 * Simple cookie jar untuk menyimpan cookies antar request.
 */
export class CookieJar {
  private cookies: Map<string, string> = new Map();

  /** Parse Set-Cookie headers dari response dan simpan */
  addFromResponse(response: Response) {
    // @ts-ignore - getSetCookie is available in Bun
    const setCookies: string[] = response.headers.getSetCookie?.() || [];
    for (const cookie of setCookies) {
      const nameValue = cookie.split(";")[0];
      const eqIndex = nameValue.indexOf("=");
      if (eqIndex > 0) {
        const name = nameValue.substring(0, eqIndex).trim();
        this.cookies.set(name, nameValue);
      }
    }
  }

  /** Dapatkan string Cookie header untuk dikirim ke request selanjutnya */
  toString(): string {
    return Array.from(this.cookies.values()).join("; ");
  }

  /** Dapatkan value dari cookie tertentu */
  get(name: string): string | undefined {
    const entry = this.cookies.get(name);
    if (!entry) return undefined;
    const eqIndex = entry.indexOf("=");
    return entry.substring(eqIndex + 1);
  }

  /** Bersihkan semua cookies */
  clear() {
    this.cookies.clear();
  }
}

/**
 * Reset database ke kondisi awal (fresh migrate + seed).
 */
export function resetDatabase() {
  try {
    execSync("php artisan migrate:fresh --seed", {
      stdio: "ignore",
      cwd: process.cwd(),
    });
  } catch (error) {
    console.error("Failed to reset database.");
  }
}

/**
 * Buat session login dan kembalikan CookieJar yang sudah ter-autentikasi.
 */
export async function login(
  email = "admin@tokokue.com",
  password = "password"
): Promise<CookieJar> {
  const jar = new CookieJar();

  // 1. GET halaman login untuk mendapatkan XSRF-TOKEN dan session cookie awal
  const csrfRes = await fetch(`${BASE_URL}/login`, { redirect: "manual" });
  jar.addFromResponse(csrfRes);

  // 2. Ambil XSRF-TOKEN untuk proteksi CSRF
  const xsrfToken = jar.get("XSRF-TOKEN");

  // 3. POST login dengan kredensial dan CSRF token
  const loginRes = await fetch(`${BASE_URL}/login`, {
    method: "POST",
    redirect: "manual",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Cookie: jar.toString(),
      ...(xsrfToken
        ? { "X-XSRF-TOKEN": decodeURIComponent(xsrfToken) }
        : {}),
    },
    body: JSON.stringify({ email, password }),
  });
  jar.addFromResponse(loginRes);

  return jar;
}

/**
 * Buat HTTP request dengan dukungan CookieJar.
 */
export async function request(
  path: string,
  options: RequestInit & { jar?: CookieJar } = {}
): Promise<Response> {
  const { jar, ...fetchOptions } = options;
  const url = `${BASE_URL}${path.startsWith("/") ? path : `/${path}`}`;

  const headers = new Headers(fetchOptions.headers || {});

  // Tambahkan cookies dari jar
  if (jar) {
    headers.set("Cookie", jar.toString());

    // Tambahkan XSRF token untuk request non-GET (CSRF protection)
    if (fetchOptions.method && fetchOptions.method !== "GET") {
      const xsrf = jar.get("XSRF-TOKEN");
      if (xsrf) {
        headers.set("X-XSRF-TOKEN", decodeURIComponent(xsrf));
      }
    }
  }

  if (!headers.has("Accept")) {
    headers.set("Accept", "application/json");
  }
  if (!headers.has("Content-Type") && fetchOptions.body) {
    headers.set("Content-Type", "application/json");
  }

  const response = await fetch(url, {
    redirect: "manual",
    ...fetchOptions,
    headers,
  });

  // Update jar dengan cookies baru dari response
  if (jar) {
    jar.addFromResponse(response);
  }

  return response;
}

import { execSync } from "child_process";

export const BASE_URL = "http://localhost:8000";

/**
 * Resets the database to a clean state.
 */
export function resetDatabase() {
  try {
    // We assume the user has php installed and is in the project root
    execSync("php artisan migrate:fresh --seed", { stdio: "ignore" });
  } catch (error) {
    console.error("Failed to reset database. Make sure PHP is installed and accessible.");
  }
}

/**
 * Helper to make a request and handle cookies (for session/auth).
 */
export async function request(path: string, options: RequestInit = {}) {
  const url = `${BASE_URL}${path.startsWith("/") ? path : `/${path}`}`;
  
  // Add common headers
  const headers = new Headers(options.headers || {});
  if (!headers.has("Accept")) {
    headers.set("Accept", "application/json");
  }
  if (!headers.has("Content-Type") && options.body) {
    headers.set("Content-Type", "application/json");
  }

  const response = await fetch(url, {
    ...options,
    headers,
  });

  return response;
}

/**
 * Helper to login a user and return the cookies.
 */
export async function login(email = "admin@tokokue.com", password = "password") {
  const response = await request("/login", {
    method: "POST",
    body: JSON.stringify({ email, password }),
    redirect: "manual",
  });

  // Fetch's get("set-cookie") joins with commas.
  // We need to be careful with commas in dates.
  // However, for testing session persistence, we mainly need laravel_session.
  // @ts-ignore
  const cookies = response.headers.getSetCookie();
  if (!cookies || cookies.length === 0) return "";

  // Return the raw cookies joined by semicolon
  return cookies.join("; ");
}

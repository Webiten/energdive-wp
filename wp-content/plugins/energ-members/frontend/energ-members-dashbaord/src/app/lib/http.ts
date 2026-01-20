const API_BASE = import.meta.env.VITE_API_BASE_URL;

export async function http(
  url: string,
  options: RequestInit = {}
) {
  const token = localStorage.getItem("access_token");

  const res = await fetch(API_BASE + url, {
    ...options,
    headers: {
      "Content-Type": "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options.headers,
    },
  });

  const data = await res.json();

  if (!res.ok) throw data;
  return data;
}

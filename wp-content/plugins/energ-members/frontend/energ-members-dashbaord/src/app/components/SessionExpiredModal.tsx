import { useSessionStore } from "../stores/sessionStore";

export function SessionExpiredModal() {
  const expired = useSessionStore((s) => s.expired);
  const reset = useSessionStore((s) => s.reset);

  if (!expired) return null;

  const handleLoginAgain = () => {
    reset();
    window.location.href = "/"; // 🔥 hard reset (cleanest)
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/40 flex items-center justify-center">
      <div className="bg-white rounded-xl p-6 w-[360px] text-center">
        <h2 className="text-lg font-semibold mb-2">Session Expired</h2>
        <p className="text-sm text-gray-600 mb-4">
          Your session has expired. Please login again.
        </p>
        <button
          onClick={handleLoginAgain}
          className="w-full bg-black text-white py-2 rounded-lg"
        >
          Login Again
        </button>
      </div>
    </div>
  );
}

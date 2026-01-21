import { create } from "zustand";

type SessionState = {
  expired: boolean;
  expire: () => void;
  reset: () => void;
};

export const useSessionStore = create<SessionState>((set) => ({
  expired: false,
  expire: () => set({ expired: true }),
  reset: () => set({ expired: false }),
}));

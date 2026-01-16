import React, { useState } from "react";
import { Bell, Search } from "lucide-react";
import { Avatar, AvatarFallback } from "./ui/avatar";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "./ui/dropdown-menu";
import { Badge } from "./ui/badge";
import { clearMeCache, getMeDisplayName, getMeInitials, useMe } from "../hooks/useMe";
import { AuthAPI } from "../lib/api";

interface TopBarProps {
  onLogout?: () => void;
}

export function TopBar({ onLogout }: TopBarProps) {
  const { me, loading } = useMe();
  const [loggingOut, setLoggingOut] = useState(false);

  const displayName = getMeDisplayName(me);
  const initials = getMeInitials(me);

  const roleLabel = me?.roleLabel ?? me?.membership?.planName ?? me?.membership?.tier ?? "Member";

  const membershipBadge =
    me?.membership?.tier ??
    (me?.membership?.planName && /pro/i.test(me.membership.planName) ? "Pro" : "Free");

  async function handleLogout() {
    if (loggingOut) return;

    setLoggingOut(true);
    try {
      // ✅ Revokes refresh token on server + clears local tokens inside AuthAPI.logout()
      await AuthAPI.logout();
    } catch {
      // Even if server logout fails (expired/already revoked), clear UI state so user isn't "stuck"
    } finally {
      clearMeCache();
      onLogout?.();
      setLoggingOut(false);
    }
  }

  return (
    <div className="h-16 border-b bg-white px-6 flex items-center justify-between sticky top-0 z-50">
      {/* Logo */}
      <div className="flex items-center gap-8">
        <div className="flex items-center gap-2 cursor-pointer">
          <div className="w-8 h-8 bg-emerald-600 rounded flex items-center justify-center">
            <span className="text-white font-bold text-sm">E</span>
          </div>
          <span className="text-xl font-semibold text-gray-900">ENERGCLUB</span>
        </div>
      </div>

      {/* Search - Future Agenda */}
      <div className="flex-1 max-w-2xl mx-8">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
          <input
            type="text"
            placeholder="Search (Coming Soon)"
            disabled
            className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-400 cursor-not-allowed"
          />
        </div>
      </div>

      {/* Right Side */}
      <div className="flex items-center gap-4">
        <button className="relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
          <Bell className="w-5 h-5 text-gray-600" />
          <span className="absolute top-1 right-1 w-2 h-2 bg-emerald-600 rounded-full"></span>
        </button>

        <DropdownMenu>
          <DropdownMenuTrigger className="focus:outline-none">
            <div className="flex items-center gap-3 hover:bg-gray-50 rounded-lg p-2 transition-colors cursor-pointer">
              <Avatar className="w-9 h-9">
                <AvatarFallback className="bg-emerald-600 text-white">
                  {loading ? "…" : initials}
                </AvatarFallback>
              </Avatar>
              <div className="text-left hidden md:block">
                <p className="text-sm font-medium text-gray-900">
                  {loading ? "Loading…" : displayName}
                </p>
                <p className="text-xs text-gray-500">{loading ? "" : roleLabel}</p>
              </div>
            </div>
          </DropdownMenuTrigger>

          <DropdownMenuContent align="end" className="w-56">
            <div className="px-2 py-2">
              <p className="text-sm font-medium">{loading ? "Loading…" : displayName}</p>
              <p className="text-xs text-gray-500">{loading ? "" : me?.email ?? ""}</p>
            </div>

            <DropdownMenuSeparator />

            <DropdownMenuItem>
              <span>My Profile</span>
            </DropdownMenuItem>
            <DropdownMenuItem>
              <span>Account Settings</span>
            </DropdownMenuItem>
            <DropdownMenuItem>
              <div className="flex items-center justify-between w-full">
                <span>Membership Status</span>
                <Badge variant="secondary" className="text-xs">
                  {membershipBadge}
                </Badge>
              </div>
            </DropdownMenuItem>

            <DropdownMenuSeparator />

            <DropdownMenuItem
              className="text-red-600"
              onClick={handleLogout}
              disabled={loggingOut}
            >
              <span>{loggingOut ? "Logging out…" : "Logout"}</span>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>
  );
}

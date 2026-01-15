import React, { useEffect, useMemo, useState } from "react";
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

interface TopBarProps {
  onLogout?: () => void;
}

type MeResponse = {
  id?: number | string;
  email?: string;

  firstName?: string;
  lastName?: string;

  // if you store plan / role / tier:
  membership?: {
    planName?: string; // "Professional Plan"
    tier?: string; // "Pro" | "Free" | "Premium"
    status?: string; // "Active"
  };

  // sometimes you may have role/capability:
  roleLabel?: string; // "Professional"
};

const ME_GET_URL = "/wp-json/wp/v2/users/me?context=edit";

function getInitials(firstName?: string, lastName?: string, email?: string) {
  const a = (firstName ?? "").trim();
  const b = (lastName ?? "").trim();
  if (a || b) {
    const i1 = a ? a[0] : "";
    const i2 = b ? b[0] : "";
    return (i1 + i2).toUpperCase() || "U";
  }
  if (email) return email.slice(0, 2).toUpperCase();
  return "U";
}

function getDisplayName(firstName?: string, lastName?: string, email?: string) {
  const name = `${firstName ?? ""} ${lastName ?? ""}`.trim();
  if (name) return name;
  if (email) return email.split("@")[0];
  return "User";
}

export function TopBar({ onLogout }: TopBarProps) {
  const [loading, setLoading] = useState(true);
  const [me, setMe] = useState<MeResponse | null>(null);

  async function fetchMe() {
    setLoading(true);
    try {
      const res = await fetch(ME_GET_URL, {
        method: "GET",
        credentials: "include",
        headers: { Accept: "application/json" },
      });

      if (res.status === 401 || res.status === 403) {
        // Not logged in; keep graceful UI. Your parent route guard should handle redirect.
        setMe(null);
        return;
      }

      if (!res.ok) {
        setMe(null);
        return;
      }

      const raw = await res.json();

      const normalized: MeResponse = {
        id: raw?.id ?? raw?.ID,
        email: raw?.email ?? raw?.user_email ?? raw?.data?.email,
        firstName: raw?.firstName ?? raw?.first_name ?? raw?.meta?.first_name ?? raw?.acf?.first_name,
        lastName: raw?.lastName ?? raw?.last_name ?? raw?.meta?.last_name ?? raw?.acf?.last_name,
        membership: raw?.membership ?? raw?.meta?.membership ?? raw?.acf?.membership,
        roleLabel: raw?.roleLabel ?? raw?.meta?.roleLabel ?? raw?.acf?.roleLabel,
      };

      setMe(normalized);
    } catch {
      setMe(null);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    fetchMe();
  }, []);

  const displayName = useMemo(
    () => getDisplayName(me?.firstName, me?.lastName, me?.email),
    [me]
  );

  const initials = useMemo(
    () => getInitials(me?.firstName, me?.lastName, me?.email),
    [me]
  );

  const roleLabel = useMemo(() => {
    // Priority: explicit role label, then membership plan/tier, else default
    if (me?.roleLabel) return me.roleLabel;
    if (me?.membership?.planName) return me.membership.planName;
    if (me?.membership?.tier) return me.membership.tier;
    return "Member";
  }, [me]);

  const membershipBadge = useMemo(() => {
    // Keep it short for the dropdown badge
    const tier = me?.membership?.tier;
    if (tier) return tier;
    const plan = me?.membership?.planName;
    if (plan) {
      if (/pro/i.test(plan)) return "Pro";
      if (/premium/i.test(plan)) return "Premium";
      if (/free/i.test(plan)) return "Free";
      return "Plan";
    }
    return "Free";
  }, [me]);

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

      {/* Right Side - Notifications & Profile */}
      <div className="flex items-center gap-4">
        {/* Notifications */}
        <button className="relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
          <Bell className="w-5 h-5 text-gray-600" />
          <span className="absolute top-1 right-1 w-2 h-2 bg-emerald-600 rounded-full"></span>
        </button>

        {/* Profile Dropdown */}
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
                <p className="text-xs text-gray-500">
                  {loading ? "" : roleLabel}
                </p>
              </div>
            </div>
          </DropdownMenuTrigger>

          <DropdownMenuContent align="end" className="w-56">
            <div className="px-2 py-2">
              <p className="text-sm font-medium">
                {loading ? "Loading…" : displayName}
              </p>
              <p className="text-xs text-gray-500">
                {loading ? "" : me?.email ?? ""}
              </p>
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

            <DropdownMenuItem className="text-red-600" onClick={onLogout}>
              <span>Logout</span>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>
  );
}

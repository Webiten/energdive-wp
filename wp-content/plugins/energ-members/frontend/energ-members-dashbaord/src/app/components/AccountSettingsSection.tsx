import React, { useEffect, useMemo, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";
import { Switch } from "./ui/switch";
import { Separator } from "./ui/separator";
import { Badge } from "./ui/badge";
import { User, Bell, CreditCard, Shield } from "lucide-react";
import { useMe } from "../hooks/useMe";

/**
 * ✅ Replace this taxonomy with your actual slugs/labels.
 * IMPORTANT: Store and send SLUGS to backend (recommended).
 */
const COMMUNITY_TAXONOMY: Array<{
  slug: string;
  label: string;
  subs: Array<{ slug: string; label: string }>;
}> = [
  {
    slug: "oil-gas",
    label: "Oil & Gas",
    subs: [
      { slug: "upstream", label: "Upstream" },
      { slug: "midstream", label: "Midstream" },
      { slug: "downstream", label: "Downstream" },
      { slug: "petrochemicals", label: "Petrochemicals" },
      { slug: "drilling-services", label: "Drilling & Services" },
    ],
  },
  {
    slug: "power-utility",
    label: "Power & Utility",
    subs: [
      { slug: "thermal", label: "Thermal" },
      { slug: "hydro", label: "Hydro" },
      { slug: "nuclear", label: "Nuclear" },
      { slug: "transmission", label: "Transmission" },
    ],
  },
  {
    slug: "safety-environment",
    label: "Safety & Environment",
    subs: [
      { slug: "hse-management", label: "HSE Management" },
      { slug: "process-safety", label: "Process Safety" },
      { slug: "fire-emergency", label: "Fire & Emergency" },
    ],
  },
];

function normalizeStringList(value: any): string[] {
  const arr: string[] = Array.isArray(value)
    ? value
    : typeof value === "string"
      ? value.split(",")
      : [];

  const cleaned = arr
    .map((s) => (typeof s === "string" ? s.trim() : ""))
    .filter(Boolean);

  const seen = new Set<string>();
  const out: string[] = [];
  for (const s of cleaned) {
    const key = s.toLowerCase();
    if (seen.has(key)) continue;
    seen.add(key);
    out.push(s);
  }
  return out;
}

export function AccountSettingsSection() {
  const { me, loading, error, updateMe } = useMe();

  const [savingProfile, setSavingProfile] = useState(false);
  const [savingPrefs, setSavingPrefs] = useState(false);
  const [savingPassword, setSavingPassword] = useState(false);

  const [saveMsg, setSaveMsg] = useState<string | null>(null);
  const [localError, setLocalError] = useState<string | null>(null);

  // -------------------------
  // Profile fields
  // -------------------------
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");

  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");

  const [jobTitle, setJobTitle] = useState("");
  const [organization, setOrganization] = useState("");

  const [country, setCountry] = useState("");
  const [industry, setIndustry] = useState("");

  // ✅ Multi-select values (slugs)
  const [communities, setCommunities] = useState<string[]>([]);
  const [subCommunities, setSubCommunities] = useState<string[]>([]);

  // -------------------------
  // Notification prefs
  // -------------------------
  const [emailNotifications, setEmailNotifications] = useState(true);
  const [weeklyDigest, setWeeklyDigest] = useState(true);
  const [eventReminders, setEventReminders] = useState(true);
  const [communityActivity, setCommunityActivity] = useState(false);

  // -------------------------
  // Password fields
  // -------------------------
  const hasPassword = useMemo(() => {
    if (!me) return true;
    if (typeof (me as any).hasPassword === "boolean") return (me as any).hasPassword;
    if (typeof (me as any).passwordSet === "boolean") return (me as any).passwordSet;
    return true;
  }, [me]);

  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmNewPassword, setConfirmNewPassword] = useState("");

  // Hydrate from /me
  useEffect(() => {
    if (!me) return;

    setFirstName((me as any).firstName ?? (me as any).first_name ?? "");
    setLastName((me as any).lastName ?? (me as any).last_name ?? "");

    setEmail((me as any).email ?? "");
    setPhone((me as any).phone ?? (me as any).identifier ?? "");

    setJobTitle((me as any).jobTitle ?? (me as any).job_title ?? "");
    setOrganization((me as any).organization ?? (me as any).company ?? "");

    setCountry((me as any).country ?? "");
    setIndustry((me as any).industry ?? "");

    // Prefer arrays returned from backend
    const cRaw =
      (me as any).communities ??
      (me as any).communities_json ??
      (me as any).community ??
      [];
    const scRaw =
      (me as any).subCommunities ??
      (me as any).sub_communities_json ??
      (me as any).sub_community ??
      [];

    setCommunities(normalizeStringList(cRaw));
    setSubCommunities(normalizeStringList(scRaw));

    const n = (me as any).notifications;
    if (n) {
      setEmailNotifications(!!n.emailNotifications);
      setWeeklyDigest(!!n.weeklyDigest);
      setEventReminders(!!n.eventReminders);
      setCommunityActivity(!!n.communityActivity);
    }
  }, [me]);

  const mergedError = localError ?? error;

  function flashMsg(msg: string) {
    setSaveMsg(msg);
    setTimeout(() => setSaveMsg(null), 2500);
  }

  // -------------------------
  // Options derived from selected communities
  // -------------------------
  const communityOptions = useMemo(() => {
    return COMMUNITY_TAXONOMY.map((c) => ({ value: c.slug, label: c.label }));
  }, []);

  const subCommunityOptions = useMemo(() => {
    const selected = new Set(communities);
    const subs: Array<{ value: string; label: string }> = [];

    for (const c of COMMUNITY_TAXONOMY) {
      if (!selected.has(c.slug)) continue;
      for (const s of c.subs) {
        subs.push({ value: s.slug, label: s.label });
      }
    }

    // de-dupe
    const seen = new Set<string>();
    return subs.filter((x) => {
      if (seen.has(x.value)) return false;
      seen.add(x.value);
      return true;
    });
  }, [communities]);

  // If user changes communities, remove sub communities that are no longer valid
  useEffect(() => {
    const allowed = new Set(subCommunityOptions.map((x) => x.value));
    setSubCommunities((prev) => prev.filter((x) => allowed.has(x)));
  }, [subCommunityOptions]);

  function onChangeMultiSelect(
    e: React.ChangeEvent<HTMLSelectElement>,
    setter: (v: string[]) => void
  ) {
    const selected = Array.from(e.target.selectedOptions).map((o) => o.value);
    setter(selected);
  }

  // -------------------------
  // Save handlers
  // -------------------------
  async function onSaveProfile() {
    setSavingProfile(true);
    setLocalError(null);
    setSaveMsg(null);

    try {
      await updateMe({
        firstName,
        lastName,
        jobTitle,
        organization,
        country,
        industry,

        communities,
        subCommunities,
      });

      flashMsg("Profile updated successfully.");
    } catch (e: any) {
      setLocalError(e?.message ?? "Something went wrong while saving profile.");
    } finally {
      setSavingProfile(false);
    }
  }

  async function onSavePreferences() {
    setSavingPrefs(true);
    setLocalError(null);
    setSaveMsg(null);

    try {
      await updateMe({
        notifications: {
          emailNotifications,
          weeklyDigest,
          eventReminders,
          communityActivity,
        },
      });

      flashMsg("Preferences updated successfully.");
    } catch (e: any) {
      setLocalError(e?.message ?? "Something went wrong while saving preferences.");
    } finally {
      setSavingPrefs(false);
    }
  }

  async function onUpdatePassword() {
    setSavingPassword(true);
    setLocalError(null);
    setSaveMsg(null);

    try {
      const np = newPassword.trim();
      const cp = currentPassword;

      if (np.length < 8) throw new Error("New password must be at least 8 characters.");
      if (np !== confirmNewPassword) throw new Error("New password and confirm password do not match.");
      if (hasPassword && !cp) throw new Error("Current password is required.");

      await updateMe({
        password: {
          ...(hasPassword ? { currentPassword: cp } : {}),
          newPassword: np,
        },
      });

      setCurrentPassword("");
      setNewPassword("");
      setConfirmNewPassword("");

      flashMsg("Password updated successfully.");
    } catch (e: any) {
      setLocalError(e?.message ?? "Something went wrong while updating password.");
    } finally {
      setSavingPassword(false);
    }
  }

  // Membership: free only
  const membership = useMemo(() => {
    return {
      planName: "Free Plan",
      status: "Active",
      price: "₹0",
      period: "",
      description: "Basic access to the platform features available to all members.",
    };
  }, []);

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-4xl mx-auto p-6 md:p-8 space-y-8">
        <div>
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">Account Settings</h1>
          <p className="text-gray-600">Manage your profile, preferences, and subscription</p>

          {loading && <p className="mt-3 text-sm text-gray-500">Loading your account details…</p>}

          {!loading && !me && (
            <div className="mt-4 p-3 rounded-lg bg-yellow-50 text-yellow-800 text-sm">
              You are not authenticated. Please log in again.
            </div>
          )}

          {!loading && mergedError && (
            <div className="mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">{mergedError}</div>
          )}

          {!loading && saveMsg && (
            <div className="mt-4 p-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm">
              {saveMsg}
            </div>
          )}
        </div>

        {/* Profile Information */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <User className="w-5 h-5 text-emerald-600" />
              Profile Information
            </CardTitle>
          </CardHeader>

          <CardContent className="space-y-5">
            {/* Names */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="firstName">First Name</Label>
                <Input
                  id="firstName"
                  value={firstName}
                  onChange={(e) => setFirstName(e.target.value)}
                  disabled={loading || savingProfile || !me}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="lastName">Last Name</Label>
                <Input
                  id="lastName"
                  value={lastName}
                  onChange={(e) => setLastName(e.target.value)}
                  disabled={loading || savingProfile || !me}
                />
              </div>
            </div>

            {/* Email + Phone */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="email">Email Address</Label>
                <Input id="email" type="email" value={email} disabled />
                <p className="text-xs text-gray-500">Email is linked to your login and cannot be changed.</p>
              </div>

              <div className="space-y-2">
                <Label htmlFor="phone">Phone</Label>
                <Input id="phone" value={phone} disabled />
                <p className="text-xs text-gray-500">Phone is linked to your login and cannot be changed.</p>
              </div>
            </div>

            {/* Job + Org */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="jobTitle">Job Title</Label>
                <Input
                  id="jobTitle"
                  value={jobTitle}
                  onChange={(e) => setJobTitle(e.target.value)}
                  disabled={loading || savingProfile || !me}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="organization">Organization</Label>
                <Input
                  id="organization"
                  value={organization}
                  onChange={(e) => setOrganization(e.target.value)}
                  disabled={loading || savingProfile || !me}
                />
              </div>
            </div>

            {/* Country + Industry */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="country">Country</Label>
                <Input
                  id="country"
                  value={country}
                  onChange={(e) => setCountry(e.target.value)}
                  disabled={loading || savingProfile || !me}
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="industry">Industry</Label>
                <Input
                  id="industry"
                  value={industry}
                  onChange={(e) => setIndustry(e.target.value)}
                  disabled={loading || savingProfile || !me}
                />
              </div>
            </div>

            {/* Communities multi-select */}
            <div className="space-y-2">
              <Label>Communities</Label>

              <select
                multiple
                value={communities}
                onChange={(e) => onChangeMultiSelect(e, setCommunities)}
                disabled={loading || savingProfile || !me}
                className="w-full min-h-[120px] rounded-md border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-200"
              >
                {communityOptions.map((o) => (
                  <option key={o.value} value={o.value}>
                    {o.label}
                  </option>
                ))}
              </select>

              <p className="text-xs text-gray-500">
                Hold Ctrl/Command to select multiple.
              </p>

              <div className="text-xs text-gray-600">
                Selected:{" "}
                {communities.length ? (
                  <span className="font-medium">{communities.length}</span>
                ) : (
                  <span className="text-gray-500">None</span>
                )}
              </div>
            </div>

            {/* Sub-Communities multi-select */}
            <div className="space-y-2">
              <Label>Sub-Communities</Label>

              <select
                multiple
                value={subCommunities}
                onChange={(e) => onChangeMultiSelect(e, setSubCommunities)}
                disabled={loading || savingProfile || !me || subCommunityOptions.length === 0}
                className="w-full min-h-[120px] rounded-md border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-200 disabled:bg-gray-50"
              >
                {subCommunityOptions.length === 0 ? (
                  <option value="" disabled>
                    Select a community first
                  </option>
                ) : (
                  subCommunityOptions.map((o) => (
                    <option key={o.value} value={o.value}>
                      {o.label}
                    </option>
                  ))
                )}
              </select>

              <div className="text-xs text-gray-600">
                Selected:{" "}
                {subCommunities.length ? (
                  <span className="font-medium">{subCommunities.length}</span>
                ) : (
                  <span className="text-gray-500">None</span>
                )}
              </div>
            </div>

            <Button
              className="bg-emerald-600 hover:bg-emerald-700"
              onClick={onSaveProfile}
              disabled={loading || savingProfile || !me}
            >
              {savingProfile ? "Saving…" : "Save Changes"}
            </Button>
          </CardContent>
        </Card>

        {/* Notification Preferences */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Bell className="w-5 h-5 text-emerald-600" />
              Notification Preferences
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="flex items-center justify-between">
              <div className="space-y-1">
                <Label>Email Notifications</Label>
                <p className="text-sm text-gray-500">Receive updates about new articles and insights</p>
              </div>
              <Switch
                checked={emailNotifications}
                onCheckedChange={setEmailNotifications}
                disabled={loading || savingPrefs || !me}
              />
            </div>
            <Separator />

            <div className="flex items-center justify-between">
              <div className="space-y-1">
                <Label>Weekly Digest</Label>
                <p className="text-sm text-gray-500">Get a weekly summary of top content</p>
              </div>
              <Switch
                checked={weeklyDigest}
                onCheckedChange={setWeeklyDigest}
                disabled={loading || savingPrefs || !me}
              />
            </div>
            <Separator />

            <div className="flex items-center justify-between">
              <div className="space-y-1">
                <Label>Event Reminders</Label>
                <p className="text-sm text-gray-500">Notifications for upcoming webinars and events</p>
              </div>
              <Switch
                checked={eventReminders}
                onCheckedChange={setEventReminders}
                disabled={loading || savingPrefs || !me}
              />
            </div>
            <Separator />

            <div className="flex items-center justify-between">
              <div className="space-y-1">
                <Label>Community Activity</Label>
                <p className="text-sm text-gray-500">Updates on discussions you're following</p>
              </div>
              <Switch
                checked={communityActivity}
                onCheckedChange={setCommunityActivity}
                disabled={loading || savingPrefs || !me}
              />
            </div>

            <Button
              className="bg-emerald-600 hover:bg-emerald-700"
              onClick={onSavePreferences}
              disabled={loading || savingPrefs || !me}
            >
              {savingPrefs ? "Saving…" : "Save Preferences"}
            </Button>
          </CardContent>
        </Card>

        {/* Membership Status - Free only */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <CreditCard className="w-5 h-5 text-emerald-600" />
              Membership Status
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex items-center justify-between p-4 bg-emerald-50 rounded-lg">
              <div>
                <div className="flex items-center gap-2 mb-1">
                  <h3 className="font-semibold text-gray-900">{membership.planName}</h3>
                  <Badge className="bg-emerald-600">{membership.status}</Badge>
                </div>
                <p className="text-sm text-gray-600">{membership.description}</p>
              </div>
              <div className="text-right">
                <p className="text-2xl font-semibold text-gray-900">{membership.price}</p>
                <p className="text-sm text-gray-500">{membership.period}</p>
              </div>
            </div>

            <p className="text-xs text-gray-500">Membership upgrades are currently not available.</p>
          </CardContent>
        </Card>

        {/* Security / Password */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Shield className="w-5 h-5 text-emerald-600" />
              Security
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label>{hasPassword ? "Change Password" : "Set Password"}</Label>

              <div className="space-y-3">
                {hasPassword && (
                  <Input
                    type="password"
                    placeholder="Current password"
                    value={currentPassword}
                    onChange={(e) => setCurrentPassword(e.target.value)}
                    disabled={loading || savingPassword || !me}
                  />
                )}

                <Input
                  type="password"
                  placeholder="New password"
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  disabled={loading || savingPassword || !me}
                />

                <Input
                  type="password"
                  placeholder="Confirm new password"
                  value={confirmNewPassword}
                  onChange={(e) => setConfirmNewPassword(e.target.value)}
                  disabled={loading || savingPassword || !me}
                />
              </div>
            </div>

            <Button
              className="bg-emerald-600 hover:bg-emerald-700"
              onClick={onUpdatePassword}
              disabled={loading || savingPassword || !me}
            >
              {savingPassword ? "Updating…" : "Update Password"}
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}

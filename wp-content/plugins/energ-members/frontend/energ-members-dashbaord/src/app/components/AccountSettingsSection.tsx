import React, { useEffect, useMemo, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";
import { Switch } from "./ui/switch";
import { Badge } from "./ui/badge";
import { Separator } from "./ui/separator";
import { User, Bell, CreditCard, Shield } from "lucide-react";
import { useMe } from "../hooks/useMe";

const ALL_SECTORS = [
  "Renewable Energy",
  "Oil & Gas",
  "Nuclear",
  "Grid Modernization",
  "Energy Storage",
  "Carbon Markets",
  "Hydrogen",
  "Policy & Regulation",
  "Market Analysis",
  "Technology Innovation",
];

function cn(...classes: Array<string | false | null | undefined>) {
  return classes.filter(Boolean).join(" ");
}

export function AccountSettingsSection() {
  const { me, loading, error, updateMe } = useMe();

  const [saving, setSaving] = useState(false);
  const [saveMsg, setSaveMsg] = useState<string | null>(null);
  const [localError, setLocalError] = useState<string | null>(null);

  // Form state (mirrors registration fields)
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [email, setEmail] = useState("");
  const [jobTitle, setJobTitle] = useState("");
  const [organization, setOrganization] = useState("");
  const [interests, setInterests] = useState<string[]>([]);

  // Notification prefs (optional)
  const [emailNotifications, setEmailNotifications] = useState(true);
  const [weeklyDigest, setWeeklyDigest] = useState(true);
  const [eventReminders, setEventReminders] = useState(true);
  const [communityActivity, setCommunityActivity] = useState(false);

  // Hydrate form from /me (single source of truth)
  useEffect(() => {
    if (!me) return;

    setFirstName(me.firstName ?? "");
    setLastName(me.lastName ?? "");
    setEmail(me.email ?? "");
    setJobTitle(me.jobTitle ?? "");
    setOrganization(me.organization ?? "");
    setInterests(Array.isArray(me.interests) ? me.interests : []);

    const n = me.notifications;
    if (n) {
      setEmailNotifications(!!n.emailNotifications);
      setWeeklyDigest(!!n.weeklyDigest);
      setEventReminders(!!n.eventReminders);
      setCommunityActivity(!!n.communityActivity);
    }
  }, [me]);

  const membership = useMemo(() => {
    return (
      me?.membership ?? {
        planName: "Professional Plan",
        status: "Active",
        price: "$49",
        period: "per month",
        description: "Full access to intelligence, events, and community features",
      }
    );
  }, [me]);

  function toggleInterest(sector: string) {
    setInterests((prev) => {
      if (prev.includes(sector)) return prev.filter((s) => s !== sector);
      return [...prev, sector];
    });
  }

  async function onSaveProfile() {
    setSaving(true);
    setSaveMsg(null);
    setLocalError(null);

    try {
      await updateMe({
        firstName,
        lastName,
        email,
        jobTitle,
        organization,
        interests,
        notifications: {
          emailNotifications,
          weeklyDigest,
          eventReminders,
          communityActivity,
        },
      });

      setSaveMsg("Changes saved successfully.");
      setTimeout(() => setSaveMsg(null), 2500);
    } catch (e: any) {
      setLocalError(e?.message ?? "Something went wrong while saving.");
    } finally {
      setSaving(false);
    }
  }

  const mergedError = localError ?? error;

  return (
    <div className="flex-1 bg-gray-50 overflow-auto">
      <div className="max-w-4xl mx-auto p-6 md:p-8 space-y-8">
        <div>
          <h1 className="text-3xl font-semibold text-gray-900 mb-2">Account Settings</h1>
          <p className="text-gray-600">Manage your profile, preferences, and subscription</p>

          {loading && <p className="mt-3 text-sm text-gray-500">Loading your account details…</p>}

          {!loading && !me && (
            <div className="mt-4 p-3 rounded-lg bg-yellow-50 text-yellow-800 text-sm">
              You are not authenticated with WordPress REST. Please log in again.
            </div>
          )}

          {!loading && mergedError && (
            <div className="mt-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
              {mergedError}
            </div>
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
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="firstName">First Name</Label>
                <Input
                  id="firstName"
                  value={firstName}
                  onChange={(e) => setFirstName(e.target.value)}
                  disabled={loading || saving || !me}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="lastName">Last Name</Label>
                <Input
                  id="lastName"
                  value={lastName}
                  onChange={(e) => setLastName(e.target.value)}
                  disabled={loading || saving || !me}
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="email">Email Address</Label>
              <Input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                disabled={loading || saving || !me}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="jobTitle">Job Title</Label>
              <Input
                id="jobTitle"
                value={jobTitle}
                onChange={(e) => setJobTitle(e.target.value)}
                disabled={loading || saving || !me}
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="organization">Organization</Label>
              <Input
                id="organization"
                value={organization}
                onChange={(e) => setOrganization(e.target.value)}
                disabled={loading || saving || !me}
              />
            </div>

            <Button
              className="bg-emerald-600 hover:bg-emerald-700"
              onClick={onSaveProfile}
              disabled={loading || saving || !me}
            >
              {saving ? "Saving…" : "Save Changes"}
            </Button>
          </CardContent>
        </Card>

        {/* Interests & Sectors */}
        <Card>
          <CardHeader>
            <CardTitle>Interests & Sectors</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <p className="text-sm text-gray-600">
              Select your areas of interest to personalize your content feed
            </p>

            <div className="flex flex-wrap gap-2">
              {ALL_SECTORS.map((sector) => {
                const selected = interests.includes(sector);
                return (
                  <Badge
                    key={sector}
                    variant="secondary"
                    onClick={() => (me ? toggleInterest(sector) : null)}
                    className={cn(
                      "px-3 py-2 select-none transition",
                      me ? "cursor-pointer" : "opacity-60 cursor-not-allowed",
                      selected
                        ? "bg-emerald-600 text-white hover:bg-emerald-700"
                        : "hover:bg-emerald-100 hover:text-emerald-700"
                    )}
                  >
                    {sector}
                  </Badge>
                );
              })}
            </div>

            <Button
              variant="outline"
              onClick={onSaveProfile}
              disabled={loading || saving || !me}
              className="border-emerald-200"
            >
              {saving ? "Saving…" : "Save Interests"}
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
                disabled={loading || saving || !me}
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
                disabled={loading || saving || !me}
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
                disabled={loading || saving || !me}
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
                disabled={loading || saving || !me}
              />
            </div>

            <Button
              className="bg-emerald-600 hover:bg-emerald-700"
              onClick={onSaveProfile}
              disabled={loading || saving || !me}
            >
              {saving ? "Saving…" : "Save Preferences"}
            </Button>
          </CardContent>
        </Card>

        {/* Membership Status */}
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
                  <h3 className="font-semibold text-gray-900">{membership.planName ?? "Plan"}</h3>
                  <Badge className="bg-emerald-600">{membership.status ?? "Active"}</Badge>
                </div>
                <p className="text-sm text-gray-600">
                  {membership.description ?? "Access to intelligence, events, and community features"}
                </p>
              </div>
              <div className="text-right">
                <p className="text-2xl font-semibold text-gray-900">{membership.price ?? "$0"}</p>
                <p className="text-sm text-gray-500">{membership.period ?? ""}</p>
              </div>
            </div>

            <div className="flex gap-3">
              <Button variant="outline">Change Plan</Button>
              <Button variant="outline" className="text-red-600 hover:text-red-700">
                Cancel Subscription
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Security */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Shield className="w-5 h-5 text-emerald-600" />
              Security
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label>Change Password</Label>
              <div className="space-y-3">
                <Input type="password" placeholder="Current password" disabled />
                <Input type="password" placeholder="New password" disabled />
                <Input type="password" placeholder="Confirm new password" disabled />
              </div>
              <p className="text-xs text-gray-500">
                Password update should be handled via a secure WP endpoint (nonce + capability checks) or your auth provider.
              </p>
            </div>
            <Button className="bg-emerald-600 hover:bg-emerald-700" disabled>
              Update Password
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}

import React, { useEffect, useMemo, useState, useRef } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";
import { Badge } from "./ui/badge";
import { User, Briefcase, Building2, LogOut, Save, X, Plus, Check, AlertCircle } from "lucide-react";
import { useMe } from "../hooks/useMe";
import { AuthAPI } from "@/app/lib/api";

/* ===================== HELPERS ===================== */

const normalizeArray = (v: any): string[] =>
  Array.isArray(v)
    ? v
    : typeof v === "string"
    ? v.split(",").map(x => x.trim()).filter(Boolean)
    : [];

/* ===================== MASTER TAXONOMY ===================== */

const COMMUNITY_MASTER = [
  { slug: "oil-gas", label: "Oil & Gas", subs: ["upstream","pipelines","refining","petrochemicals","cgd","lpg","retail","oil-markets"] },
  { slug: "power-generation", label: "Power Generation", subs: ["thermal","nuclear"] },
  { slug: "renewables", label: "Renewables", subs: ["solar","wind","hydro","biopower","cogeneration","waste-to-energy"] },
  { slug: "new-energies", label: "New Energies", subs: ["green-hydrogen","e-fuels"] },
  { slug: "energy-storage-systems", label: "Energy Storage Systems", subs: ["bess","pumped-hydro","caes","thermal","flywheel"] },
  { slug: "sustainability", label: "Sustainability", subs: ["energy-efficiency","environment","industrial-process-safety"] },
];

const COMMON_SUB_INDUSTRIES = [
  "operations","engineering","projects / epc","procurement",
  "supply-chain","sales / bd","finance","legal / compliance",
  "digital / it","hse / safety",
];

/* ===================== MAIN ===================== */

export function AccountSettingsSection() {
  const { me, updateMe } = useMe();
  const hydratedRef = useRef(false);

  /* ---------- STATE ---------- */

  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [jobTitle, setJobTitle] = useState("");
  const [organization, setOrganization] = useState("");

  const [communities, setCommunities] = useState<string[]>([]);
  const [subCommunities, setSubCommunities] = useState<string[]>([]);
  const [industry, setIndustry] = useState("");
  const [subIndustry, setSubIndustry] = useState("");

  const [isSaving, setIsSaving] = useState(false);
  const [saveSuccess, setSaveSuccess] = useState(false);
  const [isLoggingOut, setIsLoggingOut] = useState(false);

  /* ---------- HYDRATE (ONCE ONLY) ---------- */

  useEffect(() => {
    if (!me || hydratedRef.current) return;

    hydratedRef.current = true;

    setFirstName(me.firstName ?? "");
    setLastName(me.lastName ?? "");
    setEmail(me.email ?? "");
    setPhone(me.phone ?? "");
    setJobTitle(me.jobTitle ?? "");
    setOrganization(me.organization ?? "");

    setCommunities(normalizeArray(me.communities));
    setSubCommunities(normalizeArray(me.sub_communities ?? me.subCommunities));

    setIndustry(me.industry ?? "");
    setSubIndustry(me.sub_industry ?? me.subIndustry ?? "");
  }, [me]);

  /* ---------- DERIVED ---------- */

  const availableCommunities = COMMUNITY_MASTER.filter(
    c => !communities.includes(c.slug)
  );

  const selectedSubOptions = useMemo(() => {
    return COMMUNITY_MASTER
      .filter(c => communities.includes(c.slug))
      .flatMap(c => c.subs);
  }, [communities]);

  /* ---------- ACTIONS ---------- */

  const addCommunity = (slug: string) => {
    setCommunities(prev => [...prev, slug]);
    setSaveSuccess(false);
  };

  const removeCommunity = (slug: string) => {
    setCommunities(prev => prev.filter(c => c !== slug));
    setSubCommunities(prev =>
      prev.filter(
        s => !COMMUNITY_MASTER.find(c => c.slug === slug)?.subs.includes(s)
      )
    );
    setSaveSuccess(false);
  };

  const toggleSubCommunity = (slug: string) => {
    setSubCommunities(prev =>
      prev.includes(slug)
        ? prev.filter(s => s !== slug)
        : [...prev, slug]
    );
    setSaveSuccess(false);
  };

  /* ---------- SAVE ---------- */

  const onSave = async () => {
    setIsSaving(true);
    setSaveSuccess(false);

    try {
      await updateMe({
        first_name: firstName,
        last_name: lastName,
        job_title: jobTitle,
        organization,

        // Single source of truth
        communities,
        sub_communities: subCommunities,

        // Legacy support
        community: communities[0] ?? "",
        sub_community: subCommunities[0] ?? "",

        industry,
        sub_industry: subIndustry,
      });

      hydratedRef.current = true;
      setSaveSuccess(true);
      
      setTimeout(() => setSaveSuccess(false), 3000);
    } catch (error) {
      console.error("Failed to save changes:", error);
    } finally {
      setIsSaving(false);
    }
  };

  /* ---------- LOGOUT ---------- */

  const handleLogout = async () => {
    setIsLoggingOut(true);
    
    try {
      // Get refresh token from localStorage
      const refreshToken = localStorage.getItem("refresh_token");
      
      // Call logout endpoint
      await AuthAPI.logout(refreshToken || undefined);
      
      // Clear tokens
      localStorage.removeItem("access_token");
      localStorage.removeItem("refresh_token");
      localStorage.removeItem("onboarding_required");
      
      // Redirect to login
      window.location.href = "/dashboard";
    } catch (error) {
      console.error("Logout failed:", error);
      // Force logout anyway
      localStorage.clear();
      window.location.href = "/dashboard";
    }
  };

  /* ---------- UI ---------- */

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
      <div className="max-w-5xl mx-auto space-y-6">
        
        {/* Header with Logout */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">Account Settings</h1>
            <p className="text-sm text-gray-600 mt-1">Manage your profile and preferences</p>
          </div>
          <Button
            variant="outline"
            onClick={handleLogout}
            disabled={isLoggingOut}
            className="border-2 border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 transition-all"
          >
            <LogOut className="w-4 h-4 mr-2" />
            {isLoggingOut ? "Logging out..." : "Logout"}
          </Button>
        </div>

        {/* Profile Information Card */}
        <Card className="shadow-lg border-0">
          <CardHeader className="bg-gradient-to-r from-emerald-50 to-blue-50 border-b">
            <CardTitle className="flex items-center gap-2 text-xl">
              <div className="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center">
                <User className="w-5 h-5 text-white" />
              </div>
              Personal Information
            </CardTitle>
          </CardHeader>

          <CardContent className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <Label htmlFor="firstName" className="text-sm font-semibold text-gray-700">
                  First Name
                </Label>
                <Input 
                  id="firstName"
                  value={firstName} 
                  onChange={e => {
                    setFirstName(e.target.value);
                    setSaveSuccess(false);
                  }}
                  className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="lastName" className="text-sm font-semibold text-gray-700">
                  Last Name
                </Label>
                <Input 
                  id="lastName"
                  value={lastName} 
                  onChange={e => {
                    setLastName(e.target.value);
                    setSaveSuccess(false);
                  }}
                  className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="email" className="text-sm font-semibold text-gray-700">
                  Email Address
                </Label>
                <div className="relative">
                  <Input 
                    id="email"
                    value={email} 
                    disabled 
                    className="h-11 bg-gray-50 border-gray-200 pr-20"
                  />
                  <Badge className="absolute right-3 top-1/2 -translate-y-1/2 bg-emerald-100 text-emerald-700 border-emerald-200">
                    Verified
                  </Badge>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="phone" className="text-sm font-semibold text-gray-700">
                  Phone Number
                </Label>
                <div className="relative">
                  <Input 
                    id="phone"
                    value={phone} 
                    disabled 
                    className="h-11 bg-gray-50 border-gray-200 pr-20"
                  />
                  <Badge className="absolute right-3 top-1/2 -translate-y-1/2 bg-emerald-100 text-emerald-700 border-emerald-200">
                    Verified
                  </Badge>
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="jobTitle" className="text-sm font-semibold text-gray-700">
                  Job Title
                </Label>
                <Input 
                  id="jobTitle"
                  value={jobTitle} 
                  onChange={e => {
                    setJobTitle(e.target.value);
                    setSaveSuccess(false);
                  }}
                  placeholder="e.g. Senior Engineer"
                  className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="organization" className="text-sm font-semibold text-gray-700">
                  Organization
                </Label>
                <Input 
                  id="organization"
                  value={organization} 
                  onChange={e => {
                    setOrganization(e.target.value);
                    setSaveSuccess(false);
                  }}
                  placeholder="Company / Organization Name"
                  className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                />
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Professional Classification Card */}
        <Card className="shadow-lg border-0">
          <CardHeader className="bg-gradient-to-r from-blue-50 to-indigo-50 border-b">
            <CardTitle className="flex items-center gap-2 text-xl">
              <div className="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <Building2 className="w-5 h-5 text-white" />
              </div>
              Professional Classification
            </CardTitle>
          </CardHeader>

          <CardContent className="p-6 space-y-8">

            {/* Selected Communities */}
            <div className="space-y-3">
              <div className="flex items-center justify-between">
                <Label className="text-base font-bold text-gray-800">Communities</Label>
                {communities.length > 0 && (
                  <span className="text-xs text-gray-500">{communities.length} selected</span>
                )}
              </div>
              
              {communities.length > 0 ? (
                <div className="flex flex-wrap gap-2">
                  {communities.map(c => {
                    const community = COMMUNITY_MASTER.find(cm => cm.slug === c);
                    return (
                      <Badge 
                        key={c} 
                        variant="secondary"
                        className="px-4 py-2 bg-emerald-100 text-emerald-800 border border-emerald-300 hover:bg-emerald-200 cursor-pointer transition-colors text-sm"
                        onClick={() => removeCommunity(c)}
                      >
                        {community?.label || c}
                        <X className="w-3.5 h-3.5 ml-2" />
                      </Badge>
                    );
                  })}
                </div>
              ) : (
                <div className="flex items-center gap-2 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                  <AlertCircle className="w-5 h-5 text-amber-600" />
                  <p className="text-sm text-amber-800">No communities selected. Add at least one community below.</p>
                </div>
              )}
            </div>

            {/* Add Communities */}
            {availableCommunities.length > 0 && (
              <div className="space-y-3">
                <Label className="text-sm font-semibold text-gray-700">Add Communities</Label>
                <div className="flex flex-wrap gap-2">
                  {availableCommunities.map(c => (
                    <Button 
                      key={c.slug} 
                      size="sm" 
                      variant="outline"
                      onClick={() => addCommunity(c.slug)}
                      className="border-2 border-gray-300 hover:border-emerald-500 hover:bg-emerald-50 transition-all"
                    >
                      <Plus className="w-3.5 h-3.5 mr-1.5" />
                      {c.label}
                    </Button>
                  ))}
                </div>
              </div>
            )}

            {/* Sub-Communities */}
            {selectedSubOptions.length > 0 && (
              <div className="space-y-3">
                <Label className="text-base font-bold text-gray-800">Sub-Communities (Optional)</Label>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                  {selectedSubOptions.map(s => {
                    const isSelected = subCommunities.includes(s);
                    return (
                      <button
                        key={s}
                        onClick={() => toggleSubCommunity(s)}
                        className={`px-4 py-2.5 rounded-lg border-2 text-sm font-medium transition-all ${
                          isSelected
                            ? "bg-blue-50 border-blue-500 text-blue-700 shadow-sm"
                            : "bg-white border-gray-300 text-gray-700 hover:border-blue-300 hover:bg-blue-50"
                        }`}
                      >
                        <div className="flex items-center justify-between gap-2">
                          <span className="capitalize">{s.replace(/-/g, ' ')}</span>
                          {isSelected && <Check className="w-4 h-4 text-blue-600" />}
                        </div>
                      </button>
                    );
                  })}
                </div>
              </div>
            )}

            {/* Industry Classification */}
            <div className="pt-4 border-t">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <Label htmlFor="industry" className="text-sm font-semibold text-gray-700">
                    Industry
                  </Label>
                  <Input 
                    id="industry"
                    value={industry} 
                    onChange={e => {
                      setIndustry(e.target.value);
                      setSaveSuccess(false);
                    }}
                    placeholder="e.g. Electrical, Power, etc."
                    className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="subIndustry" className="text-sm font-semibold text-gray-700">
                    Sub-Industry / Specialization
                  </Label>
                  <Input
                    id="subIndustry"
                    value={subIndustry}
                    onChange={e => {
                      setSubIndustry(e.target.value);
                      setSaveSuccess(false);
                    }}
                    placeholder="e.g. Operations, Engineering, etc."
                    className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                  />
                  <p className="text-xs text-gray-500 italic">
                    Common: {COMMON_SUB_INDUSTRIES.slice(0, 5).join(", ")}, etc.
                  </p>
                </div>
              </div>
            </div>

          </CardContent>
        </Card>

        {/* Save Button */}
        <div className="flex items-center justify-end gap-4 pt-4">
          {saveSuccess && (
            <div className="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-200">
              <Check className="w-4 h-4" />
              <span className="text-sm font-medium">Changes saved successfully</span>
            </div>
          )}
          
          <Button 
            onClick={onSave}
            disabled={isSaving}
            className="bg-emerald-600 hover:bg-emerald-700 px-8 h-12 text-base shadow-lg"
          >
            {isSaving ? (
              <>
                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2" />
                Saving...
              </>
            ) : (
              <>
                <Save className="w-4 h-4 mr-2" />
                Save Changes
              </>
            )}
          </Button>
        </div>

      </div>
    </div>
  );
}
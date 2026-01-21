import React, { useEffect, useMemo, useState, useRef } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "./ui/card";
import { Button } from "./ui/button";
import { Input } from "./ui/input";
import { Label } from "./ui/label";
import { Badge } from "./ui/badge";
import { User } from "lucide-react";
import { useMe } from "../hooks/useMe";

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
  };

  const removeCommunity = (slug: string) => {
    setCommunities(prev => prev.filter(c => c !== slug));
    setSubCommunities(prev =>
      prev.filter(
        s => !COMMUNITY_MASTER.find(c => c.slug === slug)?.subs.includes(s)
      )
    );
  };

  const toggleSubCommunity = (slug: string) => {
    setSubCommunities(prev =>
      prev.includes(slug)
        ? prev.filter(s => s !== slug)
        : [...prev, slug]
    );
  };

  /* ---------- SAVE ---------- */

  const onSave = async () => {
    await updateMe({
      first_name: firstName,
      last_name: lastName,
      job_title: jobTitle,
      organization,

      // ✅ SINGLE SOURCE OF TRUTH
      communities,
      sub_communities: subCommunities,

      // legacy support (backend expects this)
      community: communities[0] ?? "",
      sub_community: subCommunities[0] ?? "",

      industry,
      sub_industry: subIndustry,
    });

    // 🔒 prevent re-hydration overwrite
    hydratedRef.current = true;
  };

  /* ---------- UI ---------- */

  return (
    <div className="max-w-4xl mx-auto space-y-8">

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <User className="w-5 h-5 text-emerald-600" />
            Profile Information
          </CardTitle>
        </CardHeader>

        <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div><Label>First Name</Label><Input value={firstName} onChange={e => setFirstName(e.target.value)} /></div>
          <div><Label>Last Name</Label><Input value={lastName} onChange={e => setLastName(e.target.value)} /></div>
          <div><Label>Email</Label><Input value={email} disabled /></div>
          <div><Label>Phone</Label><Input value={phone} disabled /></div>
          <div><Label>Job Title</Label><Input value={jobTitle} onChange={e => setJobTitle(e.target.value)} /></div>
          <div><Label>Organization</Label><Input value={organization} onChange={e => setOrganization(e.target.value)} /></div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Professional Classification</CardTitle>
        </CardHeader>

        <CardContent className="space-y-6">

          <div>
            <Label>Selected Communities</Label>
            <div className="flex flex-wrap gap-2 mt-2">
              {communities.map(c => (
                <Badge key={c} onClick={() => removeCommunity(c)} className="cursor-pointer">
                  {c} ✕
                </Badge>
              ))}
            </div>
          </div>

          <div>
            <Label>Add More Communities</Label>
            <div className="flex flex-wrap gap-2 mt-2">
              {availableCommunities.map(c => (
                <Button key={c.slug} size="sm" variant="outline" onClick={() => addCommunity(c.slug)}>
                  + {c.label}
                </Button>
              ))}
            </div>
          </div>

          <div>
            <Label>Sub-Communities</Label>
            <div className="flex flex-wrap gap-2 mt-2">
              {selectedSubOptions.map(s => (
                <button
                  key={s}
                  onClick={() => toggleSubCommunity(s)}
                  className={`px-3 py-1 rounded border ${
                    subCommunities.includes(s)
                      ? "bg-emerald-100 border-emerald-400"
                      : "bg-white"
                  }`}
                >
                  {s}
                </button>
              ))}
            </div>
          </div>

          <div>
            <Label>Industry</Label>
            <Input value={industry} onChange={e => setIndustry(e.target.value)} />
          </div>

          <div>
            <Label>Sub-Industry</Label>
            <Input
              value={subIndustry}
              onChange={e => setSubIndustry(e.target.value)}
              placeholder={COMMON_SUB_INDUSTRIES.join(", ")}
            />
          </div>

          <Button onClick={onSave}>Save Changes</Button>
        </CardContent>
      </Card>
    </div>
  );
}

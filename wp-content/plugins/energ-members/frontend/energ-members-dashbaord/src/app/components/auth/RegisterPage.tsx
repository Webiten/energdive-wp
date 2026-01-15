import { useState, useEffect } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "../ui/select";
import { CheckCircle, Loader2, Phone, AlertCircle, RefreshCw } from "lucide-react";
import { AuthAPI } from "@/app/lib/api";

interface RegisterPageProps {
  email: string;
  onRegistrationComplete: () => void;
}

// Country codes for mobile number
const countryCodes = [
  { value: "+1", label: "+1 (US/Canada)", flag: "🇺🇸" },
  { value: "+44", label: "+44 (UK)", flag: "🇬🇧" },
  { value: "+91", label: "+91 (India)", flag: "🇮🇳" },
  { value: "+49", label: "+49 (Germany)", flag: "🇩🇪" },
  { value: "+33", label: "+33 (France)", flag: "🇫🇷" },
  { value: "+86", label: "+86 (China)", flag: "🇨🇳" },
  { value: "+81", label: "+81 (Japan)", flag: "🇯🇵" },
  { value: "+61", label: "+61 (Australia)", flag: "🇦🇺" },
  { value: "+971", label: "+971 (UAE)", flag: "🇦🇪" },
  { value: "+65", label: "+65 (Singapore)", flag: "🇸🇬" },
];

// ===== REAL Communities/Sub-Communities =====
const communities = [
  { value: "oil-gas", label: "Oil & Gas" },
  { value: "power-generation", label: "Power Generation" },
  { value: "renewables", label: "Renewables" },
  { value: "transmission", label: "Transmission" },
  { value: "distribution", label: "Distribution" },
  { value: "electricity-markets", label: "Electricity Markets" },
  { value: "new-energies", label: "New Energies" },
  { value: "energy-storage-systems", label: "Energy Storage Systems" },
  { value: "sustainability", label: "Sustainability" },
];

const subCommunityMap: Record<string, Array<{ value: string; label: string }>> = {
  "oil-gas": [
    { value: "upstream", label: "Upstream" },
    { value: "pipelines", label: "Pipelines" },
    { value: "refining", label: "Refining" },
    { value: "petrochemicals", label: "Petrochemicals" },
    { value: "cgd", label: "CGD" },
    { value: "lpg", label: "LPG" },
    { value: "retail", label: "Retail" },
    { value: "oil-markets", label: "Oil Markets" },
  ],
  "power-generation": [
    { value: "thermal", label: "Thermal" },
    { value: "nuclear", label: "Nuclear" },
  ],
  renewables: [
    { value: "solar", label: "Solar" },
    { value: "wind", label: "Wind" },
    { value: "hydro", label: "Hydro" },
    { value: "biopower", label: "Biopower" },
    { value: "cogeneration", label: "Cogeneration" },
    { value: "waste-to-energy", label: "Waste-to-Energy" },
  ],
  transmission: [{ value: "smart-grid", label: "Smart Grid" }],
  distribution: [
    { value: "smart-meters-ami", label: "Smart Meters & AMI" },
    { value: "ev-charging", label: "EV Charging" },
    { value: "data-centres", label: "Data Centres" },
    { value: "smart-cities", label: "Smart Cities" },
    { value: "railways-metros", label: "Railways & Metros" },
  ],
  "electricity-markets": [
    { value: "power-markets", label: "Power Markets" },
    { value: "carbon-markets", label: "Carbon Markets" },
    { value: "rco", label: "RCO" },
  ],
  "new-energies": [
    { value: "green-hydrogen", label: "Green Hydrogen" },
    { value: "e-fuels", label: "E-Fuels" },
  ],
  "energy-storage-systems": [
    { value: "bess", label: "BESS" },
    { value: "pumped-hydro", label: "Pumped Hydro" },
    { value: "caes", label: "CAES" },
    { value: "thermal", label: "Thermal" },
    { value: "flywheel", label: "Flywheel" },
  ],
  sustainability: [
    { value: "energy-efficiency", label: "Energy Efficiency" },
    { value: "occupational-health", label: "Occupational Health" },
    { value: "industrial-process-safety", label: "Industrial & Process Safety" },
    { value: "environment", label: "Environment" },
  ],
};

// ===== REAL Industries =====
const industries = [
  { value: "agriculture", label: "Agriculture" },
  { value: "automobile", label: "Automobile" },
  { value: "aviation", label: "Aviation" },
  { value: "battery-storage", label: "Battery & Storage" },
  { value: "beauty-wellness", label: "Beauty & Wellness" },
  { value: "bfsi", label: "BFSI" },
  { value: "chemical", label: "Chemical" },
  { value: "construction-material", label: "Construction Material" },
  { value: "consulting", label: "Consulting" },
  { value: "consumer-durables", label: "Consumer Durables" },
  { value: "distribution", label: "Distribution" },
  { value: "e-commerce", label: "E-Commerce" },
  { value: "electrical", label: "Electrical" },
  { value: "electricity-markets", label: "Electricity Markets" },
  { value: "energy-efficiency-management", label: "Energy Efficiency Management" },
  { value: "engineering", label: "Engineering" },
  { value: "entertainment", label: "Entertainment" },
  { value: "environment", label: "Environment" },
  { value: "ev-charging", label: "EV Charging" },
  { value: "exporters-importers", label: "Exporters-Importers" },
  { value: "facility-management", label: "Facility Management" },
  { value: "fmcg", label: "FMCG" },
  { value: "gems", label: "Gems" },
  { value: "government", label: "Government" },
  { value: "healthcare", label: "Healthcare" },
  { value: "hotels", label: "Hotels" },
  { value: "it", label: "Information Technology (IT)" },
  { value: "infrastructure", label: "Infrastructure" },
  { value: "institutes-educational", label: "Institutes - Educational" },
  { value: "iron-steel", label: "Iron & Steel" },
  { value: "ites", label: "ITES" },
  { value: "leather", label: "Leather" },
  { value: "lighting", label: "Lighting" },
  { value: "logistics", label: "Logistics" },
  { value: "media", label: "Media" },
  { value: "mining", label: "Mining" },
  { value: "ngos", label: "NGOs" },
  { value: "office-automation", label: "Office Automation" },
  { value: "oil-gas", label: "Oil & Gas" },
  { value: "pharmaceuticals", label: "Pharmaceuticals" },
  { value: "power", label: "Power" },
  { value: "publishing", label: "Publishing" },
  { value: "railways", label: "Railways" },
  { value: "renewable", label: "Renewable" },
  { value: "retail", label: "Retail" },
  { value: "shipping", label: "Shipping" },
  { value: "sports", label: "Sports" },
  { value: "telecommunication", label: "Telecommunication" },
  { value: "textile", label: "Textile" },
  { value: "tourism", label: "Tourism" },
  { value: "transmission", label: "Transmission" },
  { value: "water-utility", label: "Water Utility" },
  { value: "wood", label: "Wood" },
];

const subIndustryMap: Record<string, Array<{ value: string; label: string }>> = {
  "agriculture": [
    { value: "agri-inputs", label: "Agri Inputs (Fertilizers & Chemicals)" },
    { value: "irrigation-water", label: "Irrigation & Water Management" },
    { value: "mechanization", label: "Farm Mechanization" },
    { value: "food-processing", label: "Food Processing" },
    { value: "cold-chain", label: "Cold Chain & Storage" },
    { value: "trading-commodities", label: "Agri Trading & Commodities" },
  ],
  "automobile": [
    { value: "oem-manufacturing", label: "OEMs & Manufacturing" },
    { value: "components", label: "Auto Components" },
    { value: "ev", label: "Electric Vehicles (EV)" },
    { value: "aftermarket", label: "Aftermarket & Services" },
    { value: "fleet-mobility", label: "Fleet & Mobility Services" },
    { value: "fuels-lubes", label: "Fuels & Lubricants" },
  ],
  "aviation": [
    { value: "airlines", label: "Airlines" },
    { value: "airports", label: "Airports & Ground Handling" },
    { value: "mro", label: "MRO (Maintenance, Repair & Overhaul)" },
    { value: "aviation-fuel", label: "Aviation Fuel & Logistics" },
    { value: "air-cargo", label: "Air Cargo" },
    { value: "leasing-finance", label: "Aircraft Leasing/Finance" },
  ],
  "battery-storage": [
    { value: "li-ion", label: "Li-ion Batteries" },
    { value: "materials", label: "Battery Materials (Cathode/Anode/Electrolyte)" },
    { value: "cell-manufacturing", label: "Cell Manufacturing" },
    { value: "pack-integration", label: "Pack Integration" },
    { value: "bess", label: "BESS (Grid Scale)" },
    { value: "recycling", label: "Recycling & Second Life" },
  ],
  "beauty-wellness": [
    { value: "personal-care", label: "Personal Care" },
    { value: "wellness-services", label: "Wellness Services" },
    { value: "cosmetics-mfg", label: "Cosmetics Manufacturing" },
    { value: "retail-distribution", label: "Retail & Distribution" },
    { value: "digital-wellness", label: "Digital Wellness/Apps" },
  ],
  "bfsi": [
    { value: "banking", label: "Banking" },
    { value: "insurance", label: "Insurance" },
    { value: "asset-management", label: "Asset Management" },
    { value: "fintech", label: "FinTech" },
    { value: "lending-nbfc", label: "Lending/NBFC" },
    { value: "risk-compliance", label: "Risk & Compliance" },
  ],
  "chemical": [
    { value: "petrochemicals", label: "Petrochemicals" },
    { value: "specialty", label: "Specialty Chemicals" },
    { value: "industrial", label: "Industrial Chemicals" },
    { value: "fertilizers", label: "Fertilizers" },
    { value: "polymers", label: "Polymers & Plastics" },
    { value: "logistics", label: "Chemical Logistics" },
  ],
  "construction-material": [
    { value: "cement", label: "Cement" },
    { value: "steel", label: "Steel (Construction)" },
    { value: "concrete", label: "Aggregates & Concrete" },
    { value: "glass", label: "Glass" },
    { value: "insulation", label: "Insulation & Building Materials" },
    { value: "epc-contractors", label: "EPC & Contractors" },
  ],
  "consulting": [
    { value: "strategy", label: "Strategy Consulting" },
    { value: "technical", label: "Technical Consulting" },
    { value: "esg", label: "ESG & Sustainability Consulting" },
    { value: "risk", label: "Risk & Compliance" },
    { value: "digital-it", label: "Digital/IT Consulting" },
    { value: "market-research", label: "Market Research" },
  ],
  "consumer-durables": [
    { value: "home-appliances", label: "Home Appliances" },
    { value: "electronics", label: "Electronics" },
    { value: "hvac", label: "HVAC & Cooling" },
    { value: "kitchen", label: "Kitchen Appliances" },
    { value: "after-sales", label: "After-sales Service" },
  ],
  "distribution": [
    { value: "power-utilities", label: "Power Distribution Utilities" },
    { value: "smart-meters-ami", label: "Smart Meters & AMI" },
    { value: "ev-charging", label: "EV Charging (Distribution side)" },
    { value: "data-centres", label: "Data Centres (grid interface)" },
    { value: "smart-cities", label: "Smart Cities" },
    { value: "railways-metros", label: "Railways/Metros (loads)" },
  ],
  "e-commerce": [
    { value: "marketplaces", label: "Marketplaces" },
    { value: "d2c", label: "D2C Brands" },
    { value: "fulfillment", label: "Logistics & Fulfillment" },
    { value: "payments", label: "Payments" },
    { value: "b2b", label: "B2B Commerce" },
    { value: "cross-border", label: "Cross-border E-Commerce" },
  ],
  "electrical": [
    { value: "switchgear", label: "Switchgear" },
    { value: "transformers", label: "Transformers" },
    { value: "cables", label: "Cables & Conductors" },
    { value: "motors-drives", label: "Motors & Drives" },
    { value: "protection-control", label: "Protection & Control" },
    { value: "automation", label: "Industrial Automation" },
  ],
  "electricity-markets": [
    { value: "trading", label: "Power Trading" },
    { value: "day-ahead-real-time", label: "Day-Ahead / Real-Time Markets" },
    { value: "carbon-markets", label: "Carbon Markets" },
    { value: "rec-rco", label: "REC / RCO" },
    { value: "analytics", label: "Market Analytics" },
    { value: "ancillary", label: "Ancillary Services" },
  ],
  "energy-efficiency-management": [
    { value: "industrial", label: "Industrial Energy Efficiency" },
    { value: "buildings", label: "Building Efficiency" },
    { value: "hvac-optimization", label: "HVAC Optimization" },
    { value: "audits-esco", label: "Audits & ESCO" },
    { value: "lighting", label: "Lighting Efficiency" },
    { value: "ems", label: "Digital Energy Management (EMS)" },
  ],
  "engineering": [
    { value: "epc", label: "EPC Services" },
    { value: "om", label: "O&M Services" },
    { value: "project-management", label: "Project Management" },
    { value: "process", label: "Process Engineering" },
    { value: "civil-structural", label: "Civil & Structural" },
    { value: "digital", label: "Digital Engineering (BIM/PLM)" },
  ],
  "entertainment": [
    { value: "production", label: "Media Production" },
    { value: "events", label: "Events & Live Entertainment" },
    { value: "ott", label: "OTT & Streaming" },
    { value: "gaming", label: "Gaming" },
    { value: "advertising", label: "Advertising & Sponsorships" },
  ],
  "environment": [
    { value: "waste", label: "Waste Management" },
    { value: "water", label: "Water & Wastewater" },
    { value: "air-quality", label: "Air Quality Monitoring" },
    { value: "compliance", label: "Environmental Compliance" },
    { value: "esg", label: "ESG Reporting" },
    { value: "carbon", label: "Carbon Accounting" },
  ],
  "ev-charging": [
    { value: "ac", label: "AC Charging" },
    { value: "dc-fast", label: "DC Fast Charging" },
    { value: "cpo", label: "Charging Network Operators" },
    { value: "hardware", label: "Charging Hardware" },
    { value: "software", label: "Software & Billing" },
    { value: "fleet", label: "Fleet Charging" },
  ],
  "exporters-importers": [
    { value: "imports", label: "Import Trading" },
    { value: "exports", label: "Export Trading" },
    { value: "customs", label: "Customs & Compliance" },
    { value: "freight", label: "Freight Forwarding" },
    { value: "commodities", label: "Commodity Trading" },
  ],
  "facility-management": [
    { value: "ifm", label: "Integrated Facility Management" },
    { value: "hvac-building", label: "HVAC & Building Services" },
    { value: "cleaning", label: "Cleaning & Hygiene" },
    { value: "security", label: "Security Services" },
    { value: "energy", label: "Energy Management in Facilities" },
  ],
  "fmcg": [
    { value: "food-bev", label: "Food & Beverages" },
    { value: "home-care", label: "Home Care" },
    { value: "personal-care", label: "Personal Care" },
    { value: "supply-chain", label: "Supply Chain & Distribution" },
    { value: "packaging", label: "Packaging" },
  ],
  "gems": [
    { value: "diamonds", label: "Diamonds" },
    { value: "jewelry-mfg", label: "Jewelry Manufacturing" },
    { value: "retail", label: "Retail & Distribution" },
    { value: "exports", label: "Exports" },
  ],
  "government": [
    { value: "ministries", label: "Ministries & Departments" },
    { value: "regulators", label: "Regulators" },
    { value: "psus", label: "Public Sector Units (PSUs)" },
    { value: "ulbs", label: "Urban Local Bodies" },
    { value: "energy-agencies", label: "Energy Agencies" },
  ],
  "healthcare": [
    { value: "hospitals", label: "Hospitals" },
    { value: "devices", label: "Medical Devices" },
    { value: "diagnostics", label: "Diagnostics Labs" },
    { value: "healthtech", label: "HealthTech" },
  ],
  "hotels": [
    { value: "hotel-chains", label: "Hotel Chains" },
    { value: "resorts", label: "Resorts" },
    { value: "business-hotels", label: "Business Hotels" },
    { value: "services", label: "Hospitality Services" },
    { value: "energy-mgmt", label: "Energy Management in Hospitality" },
  ],
  "it": [
    { value: "products", label: "Software Products" },
    { value: "services", label: "IT Services" },
    { value: "cloud-data", label: "Cloud & Data" },
    { value: "cybersecurity", label: "Cybersecurity" },
    { value: "ai-analytics", label: "AI/Analytics" },
    { value: "iot", label: "IoT Platforms" },
  ],
  "infrastructure": [
    { value: "roads", label: "Roads & Highways" },
    { value: "ports", label: "Ports" },
    { value: "airports", label: "Airports" },
    { value: "industrial-parks", label: "Industrial Parks" },
    { value: "urban", label: "Urban Infrastructure" },
    { value: "ppp", label: "PPP Projects" },
  ],
  "institutes-educational": [
    { value: "universities", label: "Universities" },
    { value: "research", label: "Research Institutes" },
    { value: "skill", label: "Skill Development" },
    { value: "training", label: "Training Providers" },
    { value: "think-tanks", label: "Think Tanks" },
  ],
  "iron-steel": [
    { value: "integrated", label: "Integrated Steel Plants" },
    { value: "secondary", label: "Secondary Steel" },
    { value: "raw-materials", label: "Mining & Raw Materials" },
    { value: "processing", label: "Steel Processing" },
    { value: "logistics", label: "Logistics" },
  ],
  "ites": [
    { value: "bpo", label: "BPO" },
    { value: "kpo", label: "KPO" },
    { value: "shared-services", label: "Shared Services" },
    { value: "contact-center", label: "Contact Centers" },
    { value: "back-office", label: "Back Office Operations" },
  ],
  "leather": [
    { value: "tanning", label: "Tanning" },
    { value: "footwear", label: "Footwear" },
    { value: "goods", label: "Leather Goods" },
    { value: "exports", label: "Exports" },
  ],
  "lighting": [
    { value: "led", label: "LED Lighting" },
    { value: "smart", label: "Smart Lighting" },
    { value: "industrial", label: "Industrial Lighting" },
    { value: "commercial", label: "Commercial Lighting" },
    { value: "controls", label: "Lighting Controls" },
  ],
  "logistics": [
    { value: "road", label: "Road Logistics" },
    { value: "rail", label: "Rail Logistics" },
    { value: "marine", label: "Shipping & Marine" },
    { value: "warehousing", label: "Warehousing" },
    { value: "cold-chain", label: "Cold Chain" },
    { value: "last-mile", label: "Last-mile Delivery" },
  ],
  "media": [
    { value: "publishing", label: "Publishing" },
    { value: "digital", label: "Digital Media" },
    { value: "broadcast", label: "Broadcasting" },
    { value: "advertising", label: "Advertising" },
    { value: "pr", label: "PR & Communications" },
  ],
  "mining": [
    { value: "coal", label: "Coal Mining" },
    { value: "metals", label: "Metal Mining" },
    { value: "processing", label: "Mineral Processing" },
    { value: "equipment", label: "Mining Equipment" },
    { value: "safety", label: "Safety & Compliance" },
  ],
  "ngos": [
    { value: "climate", label: "Climate NGOs" },
    { value: "community", label: "Community Development" },
    { value: "policy", label: "Policy & Advocacy" },
    { value: "research", label: "Research & Outreach" },
  ],
  "office-automation": [
    { value: "printing", label: "Printing & Imaging" },
    { value: "collaboration", label: "Collaboration Tools" },
    { value: "document-mgmt", label: "Document Management" },
    { value: "hardware", label: "Hardware & Devices" },
    { value: "workplace-it", label: "Workplace IT" },
  ],
  "oil-gas": [
    { value: "upstream", label: "Upstream" },
    { value: "midstream", label: "Midstream (Pipelines/Storage)" },
    { value: "downstream", label: "Downstream (Refining/Marketing)" },
    { value: "cgd", label: "CGD / City Gas" },
    { value: "lpg", label: "LPG" },
    { value: "retail-fuels", label: "Retail Fuels" },
    { value: "trading", label: "Trading & Markets" },
  ],
  "pharmaceuticals": [
    { value: "apis", label: "APIs & Bulk Drugs" },
    { value: "formulations", label: "Formulations" },
    { value: "clinical", label: "Clinical Research" },
    { value: "manufacturing", label: "Manufacturing & QA" },
    { value: "distribution", label: "Distribution" },
  ],
  "power": [
    { value: "generation", label: "Generation" },
    { value: "transmission", label: "Transmission" },
    { value: "distribution", label: "Distribution" },
    { value: "trading", label: "Power Trading" },
    { value: "om", label: "O&M Services" },
    { value: "equipment-epc", label: "Equipment & EPC" },
  ],
  "publishing": [
    { value: "magazines", label: "Magazines" },
    { value: "journals", label: "Journals" },
    { value: "digital", label: "Digital Publishing" },
    { value: "events", label: "Events & Conferences" },
    { value: "reports", label: "Research Reports" },
  ],
  "railways": [
    { value: "operators", label: "Rail Operators" },
    { value: "metros", label: "Metro Rail" },
    { value: "rolling-stock", label: "Rolling Stock" },
    { value: "signaling", label: "Signaling & Electrification" },
    { value: "stations", label: "Stations & Infrastructure" },
  ],
  "renewable": [
    { value: "solar", label: "Solar" },
    { value: "wind", label: "Wind" },
    { value: "hydro", label: "Hydro" },
    { value: "biomass", label: "Biomass/Biopower" },
    { value: "waste-to-energy", label: "Waste-to-Energy" },
    { value: "cogeneration", label: "Cogeneration" },
  ],
  "retail": [
    { value: "modern", label: "Modern Retail" },
    { value: "traditional", label: "Traditional Retail" },
    { value: "b2b", label: "B2B Retail" },
    { value: "e-retail", label: "E-Retail" },
  ],
  "shipping": [
    { value: "lines", label: "Shipping Lines" },
    { value: "ports-terminals", label: "Ports & Terminals" },
    { value: "shipbuilding", label: "Shipbuilding" },
    { value: "marine-logistics", label: "Marine Logistics" },
    { value: "bunkering", label: "Bunkering & Fuels" },
  ],
  "sports": [
    { value: "leagues", label: "Leagues & Federations" },
    { value: "venues", label: "Events & Venues" },
    { value: "media", label: "Sports Media" },
    { value: "sponsorships", label: "Sponsorships & Branding" },
  ],
  "telecommunication": [
    { value: "operators", label: "Network Operators" },
    { value: "towers", label: "Tower Companies" },
    { value: "fiber", label: "Fiber & Broadband" },
    { value: "data-centers", label: "Data Centers & Cloud Connectivity" },
    { value: "5g-iot", label: "5G / IoT" },
  ],
  "textile": [
    { value: "spinning", label: "Spinning" },
    { value: "weaving", label: "Weaving" },
    { value: "processing", label: "Processing/Dyeing" },
    { value: "apparel", label: "Apparel Manufacturing" },
    { value: "technical", label: "Technical Textiles" },
    { value: "exports", label: "Exports" },
  ],
  "tourism": [
    { value: "operators", label: "Tour Operators" },
    { value: "travel-tech", label: "Travel Tech" },
    { value: "dmc", label: "Destination Management" },
    { value: "services", label: "Hospitality Services" },
    { value: "aviation-linkage", label: "Aviation Linkage" },
  ],
  "transmission": [
    { value: "utilities", label: "Transmission Utilities" },
    { value: "substations", label: "Substations" },
    { value: "modernization", label: "Grid Modernization" },
    { value: "smart-grid", label: "Smart Grid" },
    { value: "hvdc-facts", label: "HVDC / FACTS" },
    { value: "protection-control", label: "Protection & Control" },
  ],
  "water-utility": [
    { value: "water-supply", label: "Water Supply" },
    { value: "wastewater", label: "Wastewater Treatment" },
    { value: "desalination", label: "Desalination" },
    { value: "smart-metering", label: "Smart Water Metering" },
    { value: "industrial-water", label: "Industrial Water" },
  ],
  "wood": [
    { value: "timber", label: "Timber" },
    { value: "plywood", label: "Plywood" },
    { value: "furniture", label: "Furniture" },
    { value: "processing", label: "Wood Processing" },
    { value: "paper-pulp", label: "Paper/Pulp linkage" },
  ],
};

const communityIndustryMap: Record<string, string[]> = {
  "oil-gas": [
    "oil-gas","chemical","engineering","logistics","mining","infrastructure","government","consulting","distribution",
    "shipping","railways","telecommunication","it","environment","construction-material","exporters-importers",
  ],
  "power-generation": [
    "power","electrical","engineering","construction-material","infrastructure","government","consulting","environment",
    "it","logistics","iron-steel","mining","consumer-durables",
  ],
  renewables: [
    "renewable","battery-storage","electrical","engineering","construction-material","infrastructure","government",
    "consulting","environment","it","logistics","mining","chemical","ev-charging",
  ],
  transmission: [
    "transmission","electrical","engineering","infrastructure","government","consulting","environment","it",
    "telecommunication","construction-material","iron-steel","logistics",
  ],
  distribution: [
    "distribution","electrical","engineering","it","telecommunication","infrastructure","government","consulting",
    "environment","consumer-durables","office-automation","retail","ev-charging","logistics",
  ],
  "electricity-markets": [
    "electricity-markets","bfsi","consulting","government","it","telecommunication","publishing","media","power","renewable",
  ],
  "new-energies": [
    "battery-storage","renewable","chemical","engineering","electrical","oil-gas","power","consulting","government","it",
    "environment","infrastructure","logistics",
  ],
  "energy-storage-systems": [
    "battery-storage","power","renewable","electrical","engineering","chemical","consulting","government","it","environment",
    "infrastructure","logistics",
  ],
  sustainability: [
    "environment","energy-efficiency-management","consulting","government","it","facility-management","engineering",
    "construction-material","power","renewable","chemical","fmcg","healthcare","mining","iron-steel","textile",
  ],
};

const countries = [
  { value: "usa", label: "United States" },
  { value: "uk", label: "United Kingdom" },
  { value: "india", label: "India" },
  { value: "germany", label: "Germany" },
  { value: "france", label: "France" },
];

/**
 * IMPORTANT:
 * Backend Identifier::detect() expects phone digits only (it strips non-digits),
 * and MSG91 mobiles format is digits only. We will standardize on digits-only:
 * e.g. +91 + 9058500798 => 919058500798
 */
function phoneIdentifierDigits(countryCode: string, mobile: string) {
  const cleanedMobile = (mobile || "").replace(/[^\d]/g, "");
  const ccDigits = (countryCode || "").replace(/[^\d]/g, ""); // remove '+'
  return `${ccDigits}${cleanedMobile}`.replace(/[^\d]/g, "");
}

/**
 * Backward compatible helper:
 * - If your AuthAPI.requestOtp currently accepts only (identifier),
 *   we call it with identifier only and then fallback to direct fetch with context.
 */
async function requestOtpWithContext(identifier: string, context: "login" | "register_phone") {
  // If AuthAPI.requestOtp was upgraded to accept (identifier, context), it will work.
  try {
    // @ts-ignore – allow both signatures
    return await AuthAPI.requestOtp(identifier, context);
  } catch (e: any) {
    // If backend error is "missing_identifier" or other, rethrow. Otherwise fallback.
    // But safer: do explicit fetch with context always if second arg is ignored.
    const BASE = "/wp-json/energ/v1";
    const res = await fetch(`${BASE}/auth/request-otp`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ identifier, context }),
    });
    const data = await res.json();
    if (!res.ok) throw data;
    return data;
  }
}

export function RegisterPage({ email, onRegistrationComplete }: RegisterPageProps) {
  const [isLoading, setIsLoading] = useState(false);
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    countryCode: "+1",
    mobile: "",
    country: "",
    state: "",
    community: "",
    subCommunity: "",
    industry: "",
    subIndustry: "",
    areaOfIndustry: "",
  });

  const [otpState, setOtpState] = useState<"idle" | "sent" | "verifying" | "verified" | "error">("idle");
  const [otp, setOtp] = useState(["", "", "", "", "", ""]);
  const [otpError, setOtpError] = useState("");
  const [resendTimer, setResendTimer] = useState(0);
  const [mobileChanged, setMobileChanged] = useState(false);

  const filteredIndustries = (() => {
    const allowed = communityIndustryMap[formData.community];
    if (!formData.community || !allowed?.length) return industries;
    return industries.filter((i) => allowed.includes(i.value));
  })();

  useEffect(() => {
    if (resendTimer > 0) {
      const timer = setTimeout(() => setResendTimer(resendTimer - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [resendTimer]);

  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => {
      const updated = { ...prev, [field]: value };

      if (field === "community") {
        updated.subCommunity = "";
        updated.areaOfIndustry = "";
        updated.industry = "";
        updated.subIndustry = "";
      }
      if (field === "industry") {
        updated.subIndustry = "";
      }
      if (field === "subCommunity") {
        updated.areaOfIndustry = "";
      }

      return updated;
    });
  };

  const handleMobileChange = (value: string) => {
    handleInputChange("mobile", value);
    if (otpState === "verified") {
      setOtpState("idle");
      setOtp(["", "", "", "", "", ""]);
      setMobileChanged(true);
    }
  };

  const handleSendOTP = async () => {
    const digitsMobile = (formData.mobile || "").replace(/[^\d]/g, "");
    if (!digitsMobile || digitsMobile.length < 8) {
      setOtpError("Please enter a valid mobile number");
      return;
    }

    setOtpState("sent");
    setResendTimer(60);
    setOtpError("");
    setMobileChanged(false);

    try {
      const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);
      await requestOtpWithContext(identifier, "register_phone");
    } catch (err: any) {
      setOtpState("error");
      setOtpError(err?.message || err?.data?.message || "Failed to send OTP. Please try again.");
      setResendTimer(0);
    }
  };

  const handleResendOTP = async () => {
    if (resendTimer > 0) return;
    await handleSendOTP();
  };

  const handleOTPChange = (index: number, value: string) => {
    if (value.length > 1) return;

    const newOtp = [...otp];
    newOtp[index] = value;
    setOtp(newOtp);
    setOtpError("");

    if (value && index < 5) {
      const nextInput = document.getElementById(`otp-${index + 1}`);
      nextInput?.focus();
    }

    if (newOtp.every((digit) => digit !== "") && index === 5) {
      void verifyOTP(newOtp.join(""));
    }
  };

  const handleOTPKeyDown = (index: number, e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === "Backspace" && !otp[index] && index > 0) {
      const prevInput = document.getElementById(`otp-${index - 1}`);
      prevInput?.focus();
    }
  };

  const verifyOTP = async (otpValue: string) => {
    setOtpState("verifying");

    try {
      const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);
      const res = await AuthAPI.verifyOtp(identifier, otpValue);

      // For phone verification flow, backend returns success + message (no tokens)
      if (res?.success !== true) {
        throw new Error(res?.message || "Invalid OTP. Please try again.");
      }

      setOtpState("verified");
      setOtpError("");
    } catch (err: any) {
      const code = err?.code || err?.data?.code;
      const message = err?.message || err?.data?.message || "Invalid OTP. Please try again.";

      setOtpState("error");
      setOtpError(message);

      if (code === "otp_expired") {
        setTimeout(() => setOtpState("sent"), 500);
      } else {
        setTimeout(() => setOtpState("sent"), 2000);
      }
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (otpState !== "verified") {
      setOtpError("Please verify your mobile number before submitting");
      return;
    }

    setIsLoading(true);
    setOtpError("");

    try {
      const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);

      await AuthAPI.completeRegistration({
        first_name: formData.firstName,
        last_name: formData.lastName,
        email: email,
        phone: identifier, // store digits-only (matches backend)
        country: formData.country,
        state: formData.state,
        community: formData.community,
        sub_community: formData.subCommunity,
        industry: formData.industry,
        sub_industry: formData.subIndustry,
        area_of_industry: formData.areaOfIndustry,
        privacy_accepted: true,
      });

      localStorage.removeItem("onboarding_required");

      setIsLoading(false);
      onRegistrationComplete();
    } catch (err: any) {
      setIsLoading(false);
      setOtpError(err?.message || err?.data?.message || "Registration failed. Please try again.");
    }
  };

  const isFormValid =
    formData.firstName &&
    formData.lastName &&
    formData.mobile &&
    formData.country &&
    formData.community &&
    formData.industry &&
    otpState === "verified";

  return (
    <div className="min-h-screen bg-gradient-to-br from-emerald-50 to-gray-50 py-12 px-6">
      <div className="max-w-3xl mx-auto">
        {/* Logo */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center gap-3 mb-4">
            <div className="w-12 h-12 bg-emerald-600 rounded-lg flex items-center justify-center">
              <span className="text-white font-bold text-xl">E</span>
            </div>
            <div className="text-left">
              <h1 className="text-2xl font-bold text-gray-900">ENERGCLUB</h1>
              <p className="text-sm text-gray-600">Energy Intelligence Platform</p>
            </div>
          </div>
        </div>

        <Card className="shadow-xl">
          <CardHeader>
            <CardTitle className="text-2xl text-center">Create Your Profile</CardTitle>
            <p className="text-center text-gray-600 text-sm mt-2">
              Complete your registration to access the platform
            </p>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Personal Information */}
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 border-b pb-2">
                  Personal Information
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="firstName">First Name *</Label>
                    <Input
                      id="firstName"
                      value={formData.firstName}
                      onChange={(e) => handleInputChange("firstName", e.target.value)}
                      placeholder="John"
                      required
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="lastName">Last Name *</Label>
                    <Input
                      id="lastName"
                      value={formData.lastName}
                      onChange={(e) => handleInputChange("lastName", e.target.value)}
                      placeholder="Doe"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="email">Email Address</Label>
                  <Input id="email" type="email" value={email} disabled className="bg-gray-50" />
                  <p className="text-xs text-gray-500">This email has been verified and cannot be changed</p>
                </div>

                {/* Mobile Number with OTP Verification */}
                <div className="space-y-3">
                  <Label htmlFor="mobile">Mobile Number *</Label>

                  <div className="flex gap-2">
                    {/* Country Code Selector */}
                    <Select
                      value={formData.countryCode}
                      onValueChange={(value) => handleInputChange("countryCode", value)}
                      disabled={otpState === "verified"}
                    >
                      <SelectTrigger className="w-[140px]">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {countryCodes.map((code) => (
                          <SelectItem key={code.value} value={code.value}>
                            <span className="flex items-center gap-2">
                              <span>{code.flag}</span>
                              <span>{code.value}</span>
                            </span>
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>

                    {/* Mobile Number Input */}
                    <div className="flex-1 flex gap-2">
                      <Input
                        id="mobile"
                        type="tel"
                        value={formData.mobile}
                        onChange={(e) => handleMobileChange(e.target.value)}
                        placeholder="555 123 4567"
                        disabled={otpState === "verified"}
                        className={otpState === "verified" ? "bg-gray-50" : ""}
                        required
                      />

                      {/* Send/Resend OTP Button */}
                      {otpState === "idle" || otpState === "error" ? (
                        <Button
                          type="button"
                          onClick={() => void handleSendOTP()}
                          className="bg-emerald-600 hover:bg-emerald-700 whitespace-nowrap"
                        >
                          <Phone className="w-4 h-4 mr-2" />
                          Send OTP
                        </Button>
                      ) : otpState === "verified" ? (
                        <div className="flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 rounded-md">
                          <CheckCircle className="w-4 h-4 text-green-600" />
                          <span className="text-sm text-green-700 font-medium">Verified</span>
                        </div>
                      ) : null}
                    </div>
                  </div>

                  <p className="text-xs text-gray-500">
                    {otpState === "idle"
                      ? "We'll send you a 6-digit code to verify your number and ensure community trust"
                      : otpState === "verified"
                      ? "✓ Mobile number verified successfully"
                      : ""}
                  </p>

                  {/* OTP Input Section */}
                  {(otpState === "sent" || otpState === "verifying" || otpState === "error") && (
                    <div className="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-4">
                      <div className="flex items-start gap-2">
                        <div className="flex-1">
                          <p className="text-sm font-medium text-gray-900">Enter Verification Code</p>
                          <p className="text-xs text-gray-600 mt-1">
                            We sent a 6-digit code to {formData.countryCode} {formData.mobile}
                          </p>
                        </div>
                        {otpState === "verifying" && <Loader2 className="w-5 h-5 text-emerald-600 animate-spin" />}
                      </div>

                      {/* OTP Input Fields */}
                      <div className="flex gap-2 justify-center">
                        {otp.map((digit, index) => (
                          <Input
                            key={index}
                            id={`otp-${index}`}
                            type="text"
                            inputMode="numeric"
                            maxLength={1}
                            value={digit}
                            onChange={(e) => handleOTPChange(index, e.target.value.replace(/[^0-9]/g, ""))}
                            onKeyDown={(e) => handleOTPKeyDown(index, e)}
                            className={`w-12 h-12 text-center text-lg font-semibold ${
                              otpState === "error" ? "border-red-500" : ""
                            }`}
                            disabled={otpState === "verifying"}
                          />
                        ))}
                      </div>

                      {otpError && (
                        <div className="flex items-center gap-2 text-red-600 text-sm">
                          <AlertCircle className="w-4 h-4" />
                          <span>{otpError}</span>
                        </div>
                      )}

                      <div className="text-center">
                        {resendTimer > 0 ? (
                          <p className="text-sm text-gray-600">
                            Resend code in <span className="font-semibold text-gray-900">{resendTimer}s</span>
                          </p>
                        ) : (
                          <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={() => void handleResendOTP()}
                            className="text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50"
                          >
                            <RefreshCw className="w-4 h-4 mr-2" />
                            Resend OTP
                          </Button>
                        )}
                      </div>
                    </div>
                  )}
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="country">Country *</Label>
                    <Select value={formData.country} onValueChange={(value) => handleInputChange("country", value)}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select country" />
                      </SelectTrigger>
                      <SelectContent>
                        {countries.map((country) => (
                          <SelectItem key={country.value} value={country.value}>
                            {country.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="state">State / Province</Label>
                    <Input
                      id="state"
                      value={formData.state}
                      onChange={(e) => handleInputChange("state", e.target.value)}
                      placeholder="Enter state or province"
                    />
                  </div>
                </div>
              </div>

              {/* Professional Classification */}
              <div className="space-y-4">
                <h3 className="text-lg font-semibold text-gray-900 border-b pb-2">
                  Professional Classification
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="community">Community *</Label>
                    <Select value={formData.community} onValueChange={(value) => handleInputChange("community", value)}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select community" />
                      </SelectTrigger>
                      <SelectContent>
                        {communities.map((comm) => (
                          <SelectItem key={comm.value} value={comm.value}>
                            {comm.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="subCommunity">Sub-Community</Label>
                    <Select
                      value={formData.subCommunity}
                      onValueChange={(value) => handleInputChange("subCommunity", value)}
                      disabled={!formData.community}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder={formData.community ? "Select sub-community" : "Select community first"} />
                      </SelectTrigger>
                      <SelectContent>
                        {formData.community &&
                          subCommunityMap[formData.community]?.map((sub) => (
                            <SelectItem key={sub.value} value={sub.value}>
                              {sub.label}
                            </SelectItem>
                          ))}
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <Label htmlFor="industry">Industry *</Label>
                    <Select
                      value={formData.industry}
                      onValueChange={(value) => handleInputChange("industry", value)}
                      disabled={!formData.community}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder={formData.community ? "Select industry" : "Select community first"} />
                      </SelectTrigger>
                      <SelectContent>
                        {filteredIndustries.map((ind) => (
                          <SelectItem key={ind.value} value={ind.value}>
                            {ind.label}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="subIndustry">Sub-Industry</Label>
                    <Select
                      value={formData.subIndustry}
                      onValueChange={(value) => handleInputChange("subIndustry", value)}
                      disabled={!formData.industry || !(subIndustryMap[formData.industry]?.length)}
                    >
                      <SelectTrigger>
                        <SelectValue
                          placeholder={
                            !formData.industry
                              ? "Select industry first"
                              : subIndustryMap[formData.industry]?.length
                              ? "Select sub-industry"
                              : "No sub-industries available"
                          }
                        />
                      </SelectTrigger>
                      <SelectContent>
                        {formData.industry &&
                          subIndustryMap[formData.industry]?.map((sub) => (
                            <SelectItem key={sub.value} value={sub.value}>
                              {sub.label}
                            </SelectItem>
                          ))}
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="areaOfIndustry">Area of Industry</Label>
                  <Select
                    value={formData.areaOfIndustry}
                    onValueChange={(value) => handleInputChange("areaOfIndustry", value)}
                    disabled={!formData.subCommunity}
                  >
                    <SelectTrigger>
                      <SelectValue placeholder={formData.subCommunity ? "Select area" : "Select sub-community first"} />
                    </SelectTrigger>
                    <SelectContent>{/* Keep UI intact; mapping can be added later */}</SelectContent>
                  </Select>
                  <p className="text-xs text-gray-500">
                    This will personalize your dashboard intelligence modules
                  </p>
                </div>
              </div>

              <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p className="text-sm text-blue-900">
                  <strong>Note:</strong> Your selections will be used to personalize your dashboard with relevant intelligence, reports, and analytics tailored to your professional interests.
                </p>
              </div>

              {otpState !== "verified" && formData.mobile && (
                <div className="bg-amber-50 border border-amber-200 rounded-lg p-4">
                  <div className="flex items-start gap-3">
                    <AlertCircle className="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                    <div>
                      <p className="text-sm font-medium text-amber-900">Mobile Verification Required</p>
                      <p className="text-sm text-amber-800 mt-1">
                        Please verify your mobile number to complete registration. This helps us maintain a trusted professional community.
                      </p>
                    </div>
                  </div>
                </div>
              )}

              <Button
                type="submit"
                className="w-full bg-emerald-600 hover:bg-emerald-700"
                disabled={isLoading || !isFormValid}
              >
                {isLoading ? (
                  <>
                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                    Creating your profile...
                  </>
                ) : (
                  <>
                    <CheckCircle className="w-4 h-4 mr-2" />
                    Complete Registration
                  </>
                )}
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}

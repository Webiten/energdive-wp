import { useEffect, useMemo, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
// import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "../ui/select";
import { Checkbox } from "../ui/checkbox";
import { CheckCircle, Loader2, Phone, AlertCircle, RefreshCw, User, Briefcase, Building2, ChevronRight, ChevronLeft, Check } from "lucide-react";
import { AuthAPI } from "@/app/lib/api";

interface RegisterPageProps {
  email: string;
  onRegistrationComplete: () => void;
}

// Country codes for mobile number
const countryCodes = [
  { value: "+91", label: "+91 (India)", flag: "🇮🇳" },
  { value: "+93", label: "+93 (Afghanistan)", flag: "🇦🇫" },
  { value: "+355", label: "+355 (Albania)", flag: "🇦🇱" },
  { value: "+213", label: "+213 (Algeria)", flag: "🇩🇿" },
  { value: "+376", label: "+376 (Andorra)", flag: "🇦🇩" },
  { value: "+244", label: "+244 (Angola)", flag: "🇦🇴" },
  { value: "+1-268", label: "+1-268 (Antigua and Barbuda)", flag: "🇦🇬" },
  { value: "+54", label: "+54 (Argentina)", flag: "🇦🇷" },
  { value: "+374", label: "+374 (Armenia)", flag: "🇦🇲" },
  { value: "+61", label: "+61 (Australia)", flag: "🇦🇺" },
  { value: "+43", label: "+43 (Austria)", flag: "🇦🇹" },
  { value: "+994", label: "+994 (Azerbaijan)", flag: "🇦🇿" },
  { value: "+1-242", label: "+1-242 (Bahamas)", flag: "🇧🇸" },
  { value: "+973", label: "+973 (Bahrain)", flag: "🇧🇭" },
  { value: "+880", label: "+880 (Bangladesh)", flag: "🇧🇩" },
  { value: "+1-246", label: "+1-246 (Barbados)", flag: "🇧🇧" },
  { value: "+375", label: "+375 (Belarus)", flag: "🇧🇾" },
  { value: "+32", label: "+32 (Belgium)", flag: "🇧🇪" },
  { value: "+501", label: "+501 (Belize)", flag: "🇧🇿" },
  { value: "+229", label: "+229 (Benin)", flag: "🇧🇯" },
  { value: "+975", label: "+975 (Bhutan)", flag: "🇧🇹" },
  { value: "+591", label: "+591 (Bolivia)", flag: "🇧🇴" },
  { value: "+387", label: "+387 (Bosnia and Herzegovina)", flag: "🇧🇦" },
  { value: "+267", label: "+267 (Botswana)", flag: "🇧🇼" },
  { value: "+55", label: "+55 (Brazil)", flag: "🇧🇷" },
  { value: "+673", label: "+673 (Brunei)", flag: "🇧🇳" },
  { value: "+359", label: "+359 (Bulgaria)", flag: "🇧🇬" },
  { value: "+226", label: "+226 (Burkina Faso)", flag: "🇧🇫" },
  { value: "+257", label: "+257 (Burundi)", flag: "🇧🇮" },
  { value: "+855", label: "+855 (Cambodia)", flag: "🇰🇭" },
  { value: "+237", label: "+237 (Cameroon)", flag: "🇨🇲" },
  { value: "+1", label: "+1 (Canada)", flag: "🇨🇦" },
  { value: "+238", label: "+238 (Cape Verde)", flag: "🇨🇻" },
  { value: "+236", label: "+236 (Central African Republic)", flag: "🇨🇫" },
  { value: "+235", label: "+235 (Chad)", flag: "🇹🇩" },
  { value: "+56", label: "+56 (Chile)", flag: "🇨🇱" },
  { value: "+86", label: "+86 (China)", flag: "🇨🇳" },
  { value: "+57", label: "+57 (Colombia)", flag: "🇨🇴" },
  { value: "+269", label: "+269 (Comoros)", flag: "🇰🇲" },
  { value: "+242", label: "+242 (Congo)", flag: "🇨🇬" },
  { value: "+506", label: "+506 (Costa Rica)", flag: "🇨🇷" },
  { value: "+385", label: "+385 (Croatia)", flag: "🇭🇷" },
  { value: "+53", label: "+53 (Cuba)", flag: "🇨🇺" },
  { value: "+357", label: "+357 (Cyprus)", flag: "🇨🇾" },
  { value: "+420", label: "+420 (Czech Republic)", flag: "🇨🇿" },
  { value: "+45", label: "+45 (Denmark)", flag: "🇩🇰" },
  { value: "+253", label: "+253 (Djibouti)", flag: "🇩🇯" },
  { value: "+1-767", label: "+1-767 (Dominica)", flag: "🇩🇲" },
  { value: "+1-809", label: "+1-809 (Dominican Republic)", flag: "🇩🇴" },
  { value: "+593", label: "+593 (Ecuador)", flag: "🇪🇨" },
  { value: "+20", label: "+20 (Egypt)", flag: "🇪🇬" },
  { value: "+503", label: "+503 (El Salvador)", flag: "🇸🇻" },
  { value: "+240", label: "+240 (Equatorial Guinea)", flag: "🇬🇶" },
  { value: "+291", label: "+291 (Eritrea)", flag: "🇪🇷" },
  { value: "+372", label: "+372 (Estonia)", flag: "🇪🇪" },
  { value: "+268", label: "+268 (Eswatini)", flag: "🇸🇿" },
  { value: "+251", label: "+251 (Ethiopia)", flag: "🇪🇹" },
  { value: "+679", label: "+679 (Fiji)", flag: "🇫🇯" },
  { value: "+358", label: "+358 (Finland)", flag: "🇫🇮" },
  { value: "+33", label: "+33 (France)", flag: "🇫🇷" },
  { value: "+241", label: "+241 (Gabon)", flag: "🇬🇦" },
  { value: "+220", label: "+220 (Gambia)", flag: "🇬🇲" },
  { value: "+995", label: "+995 (Georgia)", flag: "🇬🇪" },
  { value: "+49", label: "+49 (Germany)", flag: "🇩🇪" },
  { value: "+233", label: "+233 (Ghana)", flag: "🇬🇭" },
  { value: "+30", label: "+30 (Greece)", flag: "🇬🇷" },
  { value: "+1-473", label: "+1-473 (Grenada)", flag: "🇬🇩" },
  { value: "+502", label: "+502 (Guatemala)", flag: "🇬🇹" },
  { value: "+224", label: "+224 (Guinea)", flag: "🇬🇳" },
  { value: "+245", label: "+245 (Guinea-Bissau)", flag: "🇬🇼" },
  { value: "+592", label: "+592 (Guyana)", flag: "🇬🇾" },
  { value: "+509", label: "+509 (Haiti)", flag: "🇭🇹" },
  { value: "+504", label: "+504 (Honduras)", flag: "🇭🇳" },
  { value: "+36", label: "+36 (Hungary)", flag: "🇭🇺" },
  { value: "+354", label: "+354 (Iceland)", flag: "🇮🇸" },
  { value: "+62", label: "+62 (Indonesia)", flag: "🇮🇩" },
  { value: "+98", label: "+98 (Iran)", flag: "🇮🇷" },
  { value: "+964", label: "+964 (Iraq)", flag: "🇮🇶" },
  { value: "+353", label: "+353 (Ireland)", flag: "🇮🇪" },
  { value: "+972", label: "+972 (Israel)", flag: "🇮🇱" },
  { value: "+39", label: "+39 (Italy)", flag: "🇮🇹" },
  { value: "+1-876", label: "+1-876 (Jamaica)", flag: "🇯🇲" },
  { value: "+81", label: "+81 (Japan)", flag: "🇯🇵" },
  { value: "+962", label: "+962 (Jordan)", flag: "🇯🇴" },
  { value: "+7", label: "+7 (Kazakhstan)", flag: "🇰🇿" },
  { value: "+254", label: "+254 (Kenya)", flag: "🇰🇪" },
  { value: "+686", label: "+686 (Kiribati)", flag: "🇰🇮" },
  { value: "+965", label: "+965 (Kuwait)", flag: "🇰🇼" },
  { value: "+996", label: "+996 (Kyrgyzstan)", flag: "🇰🇬" },
  { value: "+856", label: "+856 (Laos)", flag: "🇱🇦" },
  { value: "+371", label: "+371 (Latvia)", flag: "🇱🇻" },
  { value: "+961", label: "+961 (Lebanon)", flag: "🇱🇧" },
  { value: "+266", label: "+266 (Lesotho)", flag: "🇱🇸" },
  { value: "+231", label: "+231 (Liberia)", flag: "🇱🇷" },
  { value: "+218", label: "+218 (Libya)", flag: "🇱🇾" },
  { value: "+423", label: "+423 (Liechtenstein)", flag: "🇱🇮" },
  { value: "+370", label: "+370 (Lithuania)", flag: "🇱🇹" },
  { value: "+352", label: "+352 (Luxembourg)", flag: "🇱🇺" },
  { value: "+261", label: "+261 (Madagascar)", flag: "🇲🇬" },
  { value: "+265", label: "+265 (Malawi)", flag: "🇲🇼" },
  { value: "+60", label: "+60 (Malaysia)", flag: "🇲🇾" },
  { value: "+960", label: "+960 (Maldives)", flag: "🇲🇻" },
  { value: "+223", label: "+223 (Mali)", flag: "🇲🇱" },
  { value: "+356", label: "+356 (Malta)", flag: "🇲🇹" },
  { value: "+692", label: "+692 (Marshall Islands)", flag: "🇲🇭" },
  { value: "+222", label: "+222 (Mauritania)", flag: "🇲🇷" },
  { value: "+230", label: "+230 (Mauritius)", flag: "🇲🇺" },
  { value: "+52", label: "+52 (Mexico)", flag: "🇲🇽" },
  { value: "+691", label: "+691 (Micronesia)", flag: "🇫🇲" },
  { value: "+373", label: "+373 (Moldova)", flag: "🇲🇩" },
  { value: "+377", label: "+377 (Monaco)", flag: "🇲🇨" },
  { value: "+976", label: "+976 (Mongolia)", flag: "🇲🇳" },
  { value: "+382", label: "+382 (Montenegro)", flag: "🇲🇪" },
  { value: "+212", label: "+212 (Morocco)", flag: "🇲🇦" },
  { value: "+258", label: "+258 (Mozambique)", flag: "🇲🇿" },
  { value: "+95", label: "+95 (Myanmar)", flag: "🇲🇲" },
  { value: "+264", label: "+264 (Namibia)", flag: "🇳🇦" },
  { value: "+674", label: "+674 (Nauru)", flag: "🇳🇷" },
  { value: "+977", label: "+977 (Nepal)", flag: "🇳🇵" },
  { value: "+31", label: "+31 (Netherlands)", flag: "🇳🇱" },
  { value: "+64", label: "+64 (New Zealand)", flag: "🇳🇿" },
  { value: "+505", label: "+505 (Nicaragua)", flag: "🇳🇮" },
  { value: "+227", label: "+227 (Niger)", flag: "🇳🇪" },
  { value: "+234", label: "+234 (Nigeria)", flag: "🇳🇬" },
  { value: "+850", label: "+850 (North Korea)", flag: "🇰🇵" },
  { value: "+389", label: "+389 (North Macedonia)", flag: "🇲🇰" },
  { value: "+47", label: "+47 (Norway)", flag: "🇳🇴" },
  { value: "+968", label: "+968 (Oman)", flag: "🇴🇲" },
  { value: "+92", label: "+92 (Pakistan)", flag: "🇵🇰" },
  { value: "+680", label: "+680 (Palau)", flag: "🇵🇼" },
  { value: "+507", label: "+507 (Panama)", flag: "🇵🇦" },
  { value: "+675", label: "+675 (Papua New Guinea)", flag: "🇵🇬" },
  { value: "+595", label: "+595 (Paraguay)", flag: "🇵🇾" },
  { value: "+51", label: "+51 (Peru)", flag: "🇵🇪" },
  { value: "+63", label: "+63 (Philippines)", flag: "🇵🇭" },
  { value: "+48", label: "+48 (Poland)", flag: "🇵🇱" },
  { value: "+351", label: "+351 (Portugal)", flag: "🇵🇹" },
  { value: "+974", label: "+974 (Qatar)", flag: "🇶🇦" },
  { value: "+40", label: "+40 (Romania)", flag: "🇷🇴" },
  { value: "+7", label: "+7 (Russia)", flag: "🇷🇺" },
  { value: "+250", label: "+250 (Rwanda)", flag: "🇷🇼" },
  { value: "+966", label: "+966 (Saudi Arabia)", flag: "🇸🇦" },
  { value: "+221", label: "+221 (Senegal)", flag: "🇸🇳" },
  { value: "+381", label: "+381 (Serbia)", flag: "🇷🇸" },
  { value: "+248", label: "+248 (Seychelles)", flag: "🇸🇨" },
  { value: "+65", label: "+65 (Singapore)", flag: "🇸🇬" },
  { value: "+421", label: "+421 (Slovakia)", flag: "🇸🇰" },
  { value: "+386", label: "+386 (Slovenia)", flag: "🇸🇮" },
  { value: "+252", label: "+252 (Somalia)", flag: "🇸🇴" },
  { value: "+27", label: "+27 (South Africa)", flag: "🇿🇦" },
  { value: "+82", label: "+82 (South Korea)", flag: "🇰🇷" },
  { value: "+211", label: "+211 (South Sudan)", flag: "🇸🇸" },
  { value: "+34", label: "+34 (Spain)", flag: "🇪🇸" },
  { value: "+94", label: "+94 (Sri Lanka)", flag: "🇱🇰" },
  { value: "+249", label: "+249 (Sudan)", flag: "🇸🇩" },
  { value: "+46", label: "+46 (Sweden)", flag: "🇸🇪" },
  { value: "+41", label: "+41 (Switzerland)", flag: "🇨🇭" },
  { value: "+963", label: "+963 (Syria)", flag: "🇸🇾" },
  { value: "+886", label: "+886 (Taiwan)", flag: "🇹🇼" },
  { value: "+992", label: "+992 (Tajikistan)", flag: "🇹🇯" },
  { value: "+255", label: "+255 (Tanzania)", flag: "🇹🇿" },
  { value: "+66", label: "+66 (Thailand)", flag: "🇹🇭" },
  { value: "+670", label: "+670 (Timor-Leste)", flag: "🇹🇱" },
  { value: "+228", label: "+228 (Togo)", flag: "🇹🇬" },
  { value: "+676", label: "+676 (Tonga)", flag: "🇹🇴" },
  { value: "+216", label: "+216 (Tunisia)", flag: "🇹🇳" },
  { value: "+90", label: "+90 (Turkey)", flag: "🇹🇷" },
  { value: "+993", label: "+993 (Turkmenistan)", flag: "🇹🇲" },
  { value: "+688", label: "+688 (Tuvalu)", flag: "🇹🇻" },
  { value: "+256", label: "+256 (Uganda)", flag: "🇺🇬" },
  { value: "+380", label: "+380 (Ukraine)", flag: "🇺🇦" },
  { value: "+971", label: "+971 (United Arab Emirates)", flag: "🇦🇪" },
  { value: "+44", label: "+44 (United Kingdom)", flag: "🇬🇧" },
  { value: "+1", label: "+1 (United States)", flag: "🇺🇸" },
  { value: "+598", label: "+598 (Uruguay)", flag: "🇺🇾" },
  { value: "+998", label: "+998 (Uzbekistan)", flag: "🇺🇿" },
  { value: "+678", label: "+678 (Vanuatu)", flag: "🇻🇺" },
  { value: "+379", label: "+379 (Vatican City)", flag: "🇻🇦" },
  { value: "+58", label: "+58 (Venezuela)", flag: "🇻🇪" },
  { value: "+84", label: "+84 (Vietnam)", flag: "🇻🇳" },
  { value: "+967", label: "+967 (Yemen)", flag: "🇾🇪" },
  { value: "+260", label: "+260 (Zambia)", flag: "🇿🇲" },
  { value: "+263", label: "+263 (Zimbabwe)", flag: "🇿🇼" }
];

// ===== Communities/Sub-Communities =====
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
    { value: "upstream", label: "Oil & Gas - Upstream" },
    { value: "pipelines", label: "Oil & Gas - Pipelines" },
    { value: "refining", label: "Oil & Gas - Refining" },
    { value: "petrochemicals", label: "Oil & Gas - Petrochemicals" },
    { value: "cgd", label: "Oil & Gas - CGD" },
    { value: "lpg", label: "Oil & Gas - LPG" },
    { value: "retail", label: "Oil & Gas - Retail" },
    { value: "oil-markets", label: "Oil & Gas - Oil Markets" },
  ],
  "power-generation": [
    { value: "thermal", label: "Power Generation - Thermal" },
    { value: "nuclear", label: "Power Generation - Nuclear" },
  ],
  renewables: [
    { value: "solar", label: "Renewables - Solar" },
    { value: "wind", label: "Renewables - Wind" },
    { value: "hydro", label: "Renewables - Hydro" },
    { value: "biopower", label: "Renewables - Biopower" },
    { value: "cogeneration", label: "Renewables - Cogeneration" },
    { value: "waste-to-energy", label: "Renewables - Waste-to-Energy" },
  ],
  transmission: [{ value: "smart-grid", label: "Transmission - Smart Grid" }],
  distribution: [
    { value: "smart-meters-ami", label: "Distribution - Smart Meters & AMI" },
    { value: "ev-charging", label: "Distribution - EV Charging" },
    { value: "data-centres", label: "Distribution - Data Centres" },
    { value: "smart-cities", label: "Distribution - Smart Cities" },
    { value: "railways-metros", label: "Distribution - Railways & Metros" },
  ],
  "electricity-markets": [
    { value: "power-markets", label: "Electricity Markets - Power Markets" },
    { value: "carbon-markets", label: "Electricity Markets - Carbon Markets" },
    { value: "rco", label: "RCO" },
  ],
  "new-energies": [
    { value: "green-hydrogen", label: " New Energies - Green Hydrogen" },
    { value: "e-fuels", label: " New Energies - E-Fuels" },
  ],
  "energy-storage-systems": [
    { value: "bess", label: "Energy Storage Systems - BESS" },
    { value: "pumped-hydro", label: "Energy Storage Systems - Pumped Hydro" },
    { value: "caes", label: "Energy Storage Systems - CAES" },
    { value: "thermal", label: "Energy Storage Systems - Thermal" },
    { value: "flywheel", label: "Energy Storage Systems - Flywheel" },
  ],
  sustainability: [
    { value: "energy-efficiency", label: "Sustainability - Energy Efficiency" },
    { value: "occupational-health", label: "Sustainability - Occupational Health" },
    { value: "industrial-process-safety", label: "Sustainability - Industrial & Process Safety" },
    { value: "environment", label: "Sustainability - Environment" },
  ],
};

// ===== Industries =====
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

const subIndustryMap: Record<string, Array<{ value: string; label: string }>> = {};

const COMMON_SUB_INDUSTRIES: Array<{ value: string; label: string }> = [
  { value: "operations", label: "Operations" },
  { value: "engineering", label: "Engineering" },
  { value: "projects", label: "Projects / EPC" },
  { value: "procurement", label: "Procurement" },
  { value: "supply-chain", label: "Supply Chain" },
  { value: "sales", label: "Sales / BD" },
  { value: "finance", label: "Finance" },
  { value: "legal", label: "Legal / Compliance" },
  { value: "digital", label: "Digital / IT" },
  { value: "hse", label: "HSE / Safety" },
];

const communityIndustryMap: Record<string, string[]> = {
  "oil-gas": ["oil-gas", "chemical", "engineering", "logistics", "mining", "infrastructure", "government", "consulting", "distribution", "shipping", "railways", "telecommunication", "it", "environment", "construction-material", "exporters-importers"],
  "power-generation": ["power", "electrical", "engineering", "construction-material", "infrastructure", "government", "consulting", "environment", "it", "logistics", "iron-steel", "mining", "consumer-durables"],
  renewables: ["renewable", "battery-storage", "electrical", "engineering", "construction-material", "infrastructure", "government", "consulting", "environment", "it", "logistics", "mining", "chemical", "ev-charging"],
  transmission: ["transmission", "electrical", "engineering", "infrastructure", "government", "consulting", "environment", "it", "telecommunication", "construction-material", "iron-steel", "logistics"],
  distribution: ["distribution", "electrical", "engineering", "it", "telecommunication", "infrastructure", "government", "consulting", "environment", "consumer-durables", "office-automation", "retail", "ev-charging", "logistics"],
  "electricity-markets": ["electricity-markets", "bfsi", "consulting", "government", "it", "telecommunication", "publishing", "media", "power", "renewable"],
  "new-energies": ["battery-storage", "renewable", "chemical", "engineering", "electrical", "oil-gas", "power", "consulting", "government", "it", "environment", "infrastructure", "logistics"],
  "energy-storage-systems": ["battery-storage", "power", "renewable", "electrical", "engineering", "chemical", "consulting", "government", "it", "environment", "infrastructure", "logistics"],
  sustainability: ["environment", "energy-efficiency-management", "consulting", "government", "it", "facility-management", "engineering", "construction-material", "power", "renewable", "chemical", "fmcg", "healthcare", "mining", "iron-steel", "textile"],
};

const countries = [
  { value: "india", label: "India" }, // always first

  { value: "afghanistan", label: "Afghanistan" },
  { value: "albania", label: "Albania" },
  { value: "algeria", label: "Algeria" },
  { value: "andorra", label: "Andorra" },
  { value: "angola", label: "Angola" },
  { value: "antigua-and-barbuda", label: "Antigua and Barbuda" },
  { value: "argentina", label: "Argentina" },
  { value: "armenia", label: "Armenia" },
  { value: "australia", label: "Australia" },
  { value: "austria", label: "Austria" },
  { value: "azerbaijan", label: "Azerbaijan" },
  { value: "bahamas", label: "Bahamas" },
  { value: "bahrain", label: "Bahrain" },
  { value: "bangladesh", label: "Bangladesh" },
  { value: "barbados", label: "Barbados" },
  { value: "belarus", label: "Belarus" },
  { value: "belgium", label: "Belgium" },
  { value: "belize", label: "Belize" },
  { value: "benin", label: "Benin" },
  { value: "bhutan", label: "Bhutan" },
  { value: "bolivia", label: "Bolivia" },
  { value: "bosnia-and-herzegovina", label: "Bosnia and Herzegovina" },
  { value: "botswana", label: "Botswana" },
  { value: "brazil", label: "Brazil" },
  { value: "brunei", label: "Brunei" },
  { value: "bulgaria", label: "Bulgaria" },
  { value: "burkina-faso", label: "Burkina Faso" },
  { value: "burundi", label: "Burundi" },
  { value: "cambodia", label: "Cambodia" },
  { value: "cameroon", label: "Cameroon" },
  { value: "canada", label: "Canada" },
  { value: "cape-verde", label: "Cape Verde" },
  { value: "central-african-republic", label: "Central African Republic" },
  { value: "chad", label: "Chad" },
  { value: "chile", label: "Chile" },
  { value: "china", label: "China" },
  { value: "colombia", label: "Colombia" },
  { value: "comoros", label: "Comoros" },
  { value: "congo", label: "Congo" },
  { value: "costa-rica", label: "Costa Rica" },
  { value: "croatia", label: "Croatia" },
  { value: "cuba", label: "Cuba" },
  { value: "cyprus", label: "Cyprus" },
  { value: "czech-republic", label: "Czech Republic" },
  { value: "denmark", label: "Denmark" },
  { value: "djibouti", label: "Djibouti" },
  { value: "dominica", label: "Dominica" },
  { value: "dominican-republic", label: "Dominican Republic" },
  { value: "ecuador", label: "Ecuador" },
  { value: "egypt", label: "Egypt" },
  { value: "el-salvador", label: "El Salvador" },
  { value: "equatorial-guinea", label: "Equatorial Guinea" },
  { value: "eritrea", label: "Eritrea" },
  { value: "estonia", label: "Estonia" },
  { value: "eswatini", label: "Eswatini" },
  { value: "ethiopia", label: "Ethiopia" },
  { value: "fiji", label: "Fiji" },
  { value: "finland", label: "Finland" },
  { value: "france", label: "France" },
  { value: "gabon", label: "Gabon" },
  { value: "gambia", label: "Gambia" },
  { value: "georgia", label: "Georgia" },
  { value: "germany", label: "Germany" },
  { value: "ghana", label: "Ghana" },
  { value: "greece", label: "Greece" },
  { value: "grenada", label: "Grenada" },
  { value: "guatemala", label: "Guatemala" },
  { value: "guinea", label: "Guinea" },
  { value: "guinea-bissau", label: "Guinea-Bissau" },
  { value: "guyana", label: "Guyana" },
  { value: "haiti", label: "Haiti" },
  { value: "honduras", label: "Honduras" },
  { value: "hungary", label: "Hungary" },
  { value: "iceland", label: "Iceland" },
  { value: "indonesia", label: "Indonesia" },
  { value: "iran", label: "Iran" },
  { value: "iraq", label: "Iraq" },
  { value: "ireland", label: "Ireland" },
  { value: "israel", label: "Israel" },
  { value: "italy", label: "Italy" },
  { value: "jamaica", label: "Jamaica" },
  { value: "japan", label: "Japan" },
  { value: "jordan", label: "Jordan" },
  { value: "kazakhstan", label: "Kazakhstan" },
  { value: "kenya", label: "Kenya" },
  { value: "kiribati", label: "Kiribati" },
  { value: "kuwait", label: "Kuwait" },
  { value: "kyrgyzstan", label: "Kyrgyzstan" },
  { value: "laos", label: "Laos" },
  { value: "latvia", label: "Latvia" },
  { value: "lebanon", label: "Lebanon" },
  { value: "lesotho", label: "Lesotho" },
  { value: "liberia", label: "Liberia" },
  { value: "libya", label: "Libya" },
  { value: "liechtenstein", label: "Liechtenstein" },
  { value: "lithuania", label: "Lithuania" },
  { value: "luxembourg", label: "Luxembourg" },
  { value: "madagascar", label: "Madagascar" },
  { value: "malawi", label: "Malawi" },
  { value: "malaysia", label: "Malaysia" },
  { value: "maldives", label: "Maldives" },
  { value: "mali", label: "Mali" },
  { value: "malta", label: "Malta" },
  { value: "marshall-islands", label: "Marshall Islands" },
  { value: "mauritania", label: "Mauritania" },
  { value: "mauritius", label: "Mauritius" },
  { value: "mexico", label: "Mexico" },
  { value: "micronesia", label: "Micronesia" },
  { value: "moldova", label: "Moldova" },
  { value: "monaco", label: "Monaco" },
  { value: "mongolia", label: "Mongolia" },
  { value: "montenegro", label: "Montenegro" },
  { value: "morocco", label: "Morocco" },
  { value: "mozambique", label: "Mozambique" },
  { value: "myanmar", label: "Myanmar" },
  { value: "namibia", label: "Namibia" },
  { value: "nauru", label: "Nauru" },
  { value: "nepal", label: "Nepal" },
  { value: "netherlands", label: "Netherlands" },
  { value: "new-zealand", label: "New Zealand" },
  { value: "nicaragua", label: "Nicaragua" },
  { value: "niger", label: "Niger" },
  { value: "nigeria", label: "Nigeria" },
  { value: "north-korea", label: "North Korea" },
  { value: "north-macedonia", label: "North Macedonia" },
  { value: "norway", label: "Norway" },
  { value: "oman", label: "Oman" },
  { value: "pakistan", label: "Pakistan" },
  { value: "palau", label: "Palau" },
  { value: "panama", label: "Panama" },
  { value: "papua-new-guinea", label: "Papua New Guinea" },
  { value: "paraguay", label: "Paraguay" },
  { value: "peru", label: "Peru" },
  { value: "philippines", label: "Philippines" },
  { value: "poland", label: "Poland" },
  { value: "portugal", label: "Portugal" },
  { value: "qatar", label: "Qatar" },
  { value: "romania", label: "Romania" },
  { value: "russia", label: "Russia" },
  { value: "rwanda", label: "Rwanda" },
  { value: "saudi-arabia", label: "Saudi Arabia" },
  { value: "senegal", label: "Senegal" },
  { value: "serbia", label: "Serbia" },
  { value: "seychelles", label: "Seychelles" },
  { value: "singapore", label: "Singapore" },
  { value: "slovakia", label: "Slovakia" },
  { value: "slovenia", label: "Slovenia" },
  { value: "somalia", label: "Somalia" },
  { value: "south-africa", label: "South Africa" },
  { value: "south-korea", label: "South Korea" },
  { value: "south-sudan", label: "South Sudan" },
  { value: "spain", label: "Spain" },
  { value: "sri-lanka", label: "Sri Lanka" },
  { value: "sudan", label: "Sudan" },
  { value: "sweden", label: "Sweden" },
  { value: "switzerland", label: "Switzerland" },
  { value: "syria", label: "Syria" },
  { value: "taiwan", label: "Taiwan" },
  { value: "tajikistan", label: "Tajikistan" },
  { value: "tanzania", label: "Tanzania" },
  { value: "thailand", label: "Thailand" },
  { value: "timor-leste", label: "Timor-Leste" },
  { value: "togo", label: "Togo" },
  { value: "tonga", label: "Tonga" },
  { value: "tunisia", label: "Tunisia" },
  { value: "turkey", label: "Turkey" },
  { value: "turkmenistan", label: "Turkmenistan" },
  { value: "tuvalu", label: "Tuvalu" },
  { value: "uganda", label: "Uganda" },
  { value: "ukraine", label: "Ukraine" },
  { value: "united-arab-emirates", label: "United Arab Emirates" },
  { value: "uk", label: "United Kingdom" },
  { value: "usa", label: "United States" },
  { value: "uruguay", label: "Uruguay" },
  { value: "uzbekistan", label: "Uzbekistan" },
  { value: "vanuatu", label: "Vanuatu" },
  { value: "vatican-city", label: "Vatican City" },
  { value: "venezuela", label: "Venezuela" },
  { value: "vietnam", label: "Vietnam" },
  { value: "yemen", label: "Yemen" },
  { value: "zambia", label: "Zambia" },
  { value: "zimbabwe", label: "Zimbabwe" }
];


function phoneIdentifierDigits(countryCode: string, mobile: string) {
  const cleanedMobile = (mobile || "").replace(/[^\d]/g, "");
  const ccDigits = (countryCode || "").replace(/[^\d]/g, "");
  return `${ccDigits}${cleanedMobile}`.replace(/[^\d]/g, "");
}

async function requestOtpWithContext(identifier: string, context: "login" | "register_phone") {
  try {
    // @ts-ignore
    return await AuthAPI.requestOtp(identifier, context);
  } catch {
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

function toggleInArray(arr: string[], value: string) {
  return arr.includes(value) ? arr.filter((x) => x !== value) : [...arr, value];
}

export function RegisterPage({ email, onRegistrationComplete }: RegisterPageProps) {
  const [isLoading, setIsLoading] = useState(false);
  const [currentStep, setCurrentStep] = useState(1);

  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    countryCode: "+91",
    mobile: "",
    country: "india",
    state: "",
    jobTitle: "",
    organization: "",
    communities: [] as string[],
    subCommunities: [] as string[],
    industry: "",
    subIndustry: "",
    areaOfIndustry: "",
  });


  const [otpState, setOtpState] = useState<"idle" | "sent" | "verifying" | "verified" | "error">("idle");
  const [otp, setOtp] = useState(["", "", "", "", "", ""])
  const [otpError, setOtpError] = useState("");
  const [resendTimer, setResendTimer] = useState(0);

  const safeSubIndustryMap = useMemo(() => subIndustryMap || {}, []);

  const availableSubCommunities = useMemo(() => {
    const set = new Map<string, { value: string; label: string }>();
    for (const c of formData.communities) {
      for (const s of subCommunityMap[c] || []) set.set(s.value, s);
    }
    return Array.from(set.values());
  }, [formData.communities]);

  const filteredIndustries = useMemo(() => {
    if (!formData.communities.length) return industries;
    const allowed = new Set<string>();
    formData.communities.forEach((c) => (communityIndustryMap[c] || []).forEach((x) => allowed.add(x)));
    return industries.filter((i) => allowed.has(i.value));
  }, [formData.communities]);

  const subIndustryOptions = useMemo(() => {
    const custom = safeSubIndustryMap[formData.industry];
    if (custom && custom.length) return custom;
    return COMMON_SUB_INDUSTRIES;
  }, [formData.industry, safeSubIndustryMap]);

  useEffect(() => {
    if (resendTimer > 0) {
      const timer = setTimeout(() => setResendTimer(resendTimer - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [resendTimer]);

  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => {
      const updated: any = { ...prev, [field]: value };
      if (field === "industry" && value) {
        updated.subIndustry = "";
      }
      return updated;
    });
    console.log("submititied", formData)
  };

  const handleMobileChange = (value: string) => {
    setFormData((prev) => ({ ...prev, mobile: value }));
    if (otpState === "verified") {
      setOtpState("idle");
      setOtp(["", "", "", "", "", ""]);
      setOtpError("");
      setResendTimer(0);
    }
  };

  const handleToggleCommunity = (value: string) => {
    setFormData((prev) => {
      const nextCommunities = toggleInArray(prev.communities, value);
      if (!nextCommunities.length) {
        return {
          ...prev,
          communities: [],
          subCommunities: [],
          industry: "",
          subIndustry: "",
          areaOfIndustry: "",
        };
      }
      const validSubs = new Set<string>();
      nextCommunities.forEach((c) => (subCommunityMap[c] || []).forEach((s) => validSubs.add(s.value)));
      const nextSubCommunities = prev.subCommunities.filter((s) => validSubs.has(s));
      const nextAllowedIndustries = new Set<string>();
      nextCommunities.forEach((c) => (communityIndustryMap[c] || []).forEach((x) => nextAllowedIndustries.add(x)));
      const nextIndustry = prev.industry && nextAllowedIndustries.has(prev.industry) ? prev.industry : "";
      const nextSubIndustry = nextIndustry ? prev.subIndustry : "";
      return {
        ...prev,
        communities: nextCommunities,
        subCommunities: nextSubCommunities,
        industry: nextIndustry,
        subIndustry: nextSubIndustry,
        areaOfIndustry: "",
      };
    });
  };

  const handleToggleSubCommunity = (value: string) => {
    setFormData((prev) => ({
      ...prev,
      subCommunities: toggleInArray(prev.subCommunities, value),
      areaOfIndustry: "",
    }));
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
    if (value && index < 5) document.getElementById(`otp-${index + 1}`)?.focus();
    if (newOtp.every((d) => d !== "") && index === 5) void verifyOTP(newOtp.join(""));
  };

  const handleOTPKeyDown = (index: number, e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === "Backspace" && !otp[index] && index > 0) document.getElementById(`otp-${index - 1}`)?.focus();
  };

  const verifyOTP = async (otpValue: string) => {
    setOtpState("verifying");
    try {
      const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);
      const res = await AuthAPI.verifyOtp(identifier, otpValue);
      if (res?.success !== true) throw new Error(res?.message || "Invalid OTP. Please try again.");
      setOtpState("verified");
      setOtpError("");
    } catch (err: any) {
      const message = err?.message || err?.data?.message || "Invalid OTP. Please try again.";
      setOtpState("error");
      setOtpError(message);
      setTimeout(() => setOtpState("sent"), 1500);
    }
  };

  // const handleSubmit = async (e: React.FormEvent) => {
  //   e.preventDefault();
  //   if (otpState !== "verified") {
  //     setOtpError("Please verify your mobile number before submitting");
  //     return;
  //   }
  //   setIsLoading(true);
  //   setOtpError("");
  //   try {
  //     const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);
  //     const primaryCommunity = formData.communities[0] || "";
  //     const primarySubCommunity = formData.subCommunities[0] || "";
  //     await AuthAPI.completeRegistration({
  //       first_name: formData.firstName,
  //       last_name: formData.lastName,
  //       email,
  //       phone: identifier,
  //       country: formData.country,
  //       state: formData.state,
  //       job_title: formData.jobTitle,
  //       organization: formData.organization,
  //       community: primaryCommunity,
  //       sub_community: primarySubCommunity,
  //       communities: formData.communities,
  //       sub_communities: formData.subCommunities,
  //       industry: formData.industry,
  //       sub_industry: formData.subIndustry,
  //       area_of_industry: formData.areaOfIndustry,
  //       privacy_accepted: true,
  //     });
  //     localStorage.removeItem("onboarding_required");
  //     setIsLoading(false);
  //     onRegistrationComplete();
  //   } catch (err: any) {
  //     setIsLoading(false);
  //     setOtpError(err?.message || err?.data?.message || "Registration failed. Please try again.");
  //   }
  // };

const handleSubmit = async (e: React.FormEvent) => {
  e.preventDefault();
  
  // ADD THIS DEBUG
  console.log("=== FORM SUBMIT DEBUG ===");
  console.log("OTP State:", otpState);
  console.log("Form Data:", formData);
  
  if (otpState !== "verified") {
    setOtpError("Please verify your mobile number before submitting");
    return;
  }
  
  setIsLoading(true);
  setOtpError("");
  
  try {
    const identifier = phoneIdentifierDigits(formData.countryCode, formData.mobile);
    const primaryCommunity = formData.communities[0] || "";
    const primarySubCommunity = formData.subCommunities[0] || "";
    
    const payload = {
      first_name: formData.firstName,
      last_name: formData.lastName,
      email,
      phone: identifier,
      country: formData.country,
      state: formData.state,
      job_title: formData.jobTitle,
      organization: formData.organization,
      community: primaryCommunity,
      sub_community: primarySubCommunity,
      communities: formData.communities,
      sub_communities: formData.subCommunities,
      industry: formData.industry,
      sub_industry: formData.subIndustry,
      area_of_industry: formData.areaOfIndustry,
      privacy_accepted: true,
    };
    
    // ADD THIS DEBUG
    console.log("=== PAYLOAD TO API ===");
    console.log(JSON.stringify(payload, null, 2));
    
    await AuthAPI.completeRegistration(payload);
    
    localStorage.removeItem("onboarding_required");
    setIsLoading(false);
    onRegistrationComplete();
  } catch (err: any) {
    // ADD THIS DEBUG
    console.log("=== REGISTRATION ERROR ===");
    console.log(err);
    
    setIsLoading(false);
    setOtpError(err?.message || err?.data?.message || "Registration failed. Please try again.");
  }
};

  // Step validation
  const isStep1Valid = formData.firstName && formData.lastName && formData.mobile && otpState === "verified" && formData.country;
  const isStep2Valid = formData.jobTitle && formData.organization;
  const isStep3Valid = formData.communities.length > 0 && formData.industry;

  const steps = [
    { number: 1, title: "Personal Details", icon: User },
    { number: 2, title: "Professional Details", icon: Briefcase },
    { number: 3, title: "Industry & Communities", icon: Building2 },
  ];

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-blue-50 py-12 px-4">
      <div className="w-full max-w-4xl">
        {/* Progress Steps */}
        <div className="mb-8">
          <div className="flex items-center justify-between max-w-2xl mx-auto">
            {steps.map((step, idx) => {
              const isActive = currentStep === step.number;
              const isCompleted = currentStep > step.number;
              const Icon = step.icon;

              return (
                <div key={step.number} className="flex items-center flex-1">
                  <div className="flex flex-col items-center flex-1">
                    <div
                      className={`w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 ${isCompleted
                        ? "bg-emerald-600 text-white shadow-lg shadow-emerald-200"
                        : isActive
                          ? "bg-emerald-600 text-white shadow-lg shadow-emerald-200 scale-110"
                          : "bg-gray-200 text-gray-500"
                        }`}
                    >
                      {isCompleted ? <Check className="w-6 h-6" /> : <Icon className="w-6 h-6" />}
                    </div>
                    <p className={`mt-2 text-sm font-medium ${isActive ? "text-emerald-600" : "text-gray-500"}`}>
                      {step.title}
                    </p>
                  </div>
                  {idx < steps.length - 1 && (
                    <div className={`h-1 flex-1 mx-2 rounded transition-all duration-300 ${currentStep > step.number ? "bg-emerald-600" : "bg-gray-200"
                      }`} />
                  )}
                </div>
              );
            })}
          </div>
        </div>

        <Card className="shadow-2xl border-0 overflow-hidden">
          <CardHeader className="bg-gradient-to-r from-emerald-600 to-emerald-500 text-white py-8">
            <CardTitle className="text-3xl text-center font-bold">Create Your Profile</CardTitle>
            <p className="text-center text-emerald-50 text-sm mt-2">
              {currentStep === 1 && "Please provide your basic contact information"}
              {currentStep === 2 && "Tell us about your professional background"}
              {currentStep === 3 && "Select your industry focus and community interests"}
            </p>
          </CardHeader>

          <CardContent className="p-8">
            <form onSubmit={handleSubmit}>
              {/* Step 1: Personal Information */}
              {currentStep === 1 && (
                <div className="space-y-6 animate-in fade-in duration-500">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <Label htmlFor="firstName" className="text-sm font-semibold text-gray-700">
                        First Name <span className="text-red-500">*</span>
                      </Label>
                      <Input
                        id="firstName"
                        value={formData.firstName}
                        onChange={(e) => handleInputChange("firstName", e.target.value)}
                        placeholder="John"
                        className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                        required
                      />
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="lastName" className="text-sm font-semibold text-gray-700">
                        Last Name <span className="text-red-500">*</span>
                      </Label>
                      <Input
                        id="lastName"
                        value={formData.lastName}
                        onChange={(e) => handleInputChange("lastName", e.target.value)}
                        placeholder="Doe"
                        className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                        required
                      />
                    </div>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="email" className="text-sm font-semibold text-gray-700">Email Address</Label>
                    <div className="relative">
                      <Input
                        id="email"
                        type="email"
                        value={email}
                        disabled
                        className="h-11 bg-gray-50 border-gray-200 pr-24"
                      />
                      <div className="absolute right-3 top-1/2 -translate-y-1/2">
                        <div className="flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-medium">
                          <CheckCircle className="w-3.5 h-3.5" />
                          Verified
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Mobile OTP Section */}
                  <div className="space-y-3">
                    <Label htmlFor="mobile" className="text-sm font-semibold text-gray-700">
                      Mobile Number <span className="text-red-500">*</span>
                    </Label>

                    <div className="flex gap-3">
                      <select
                        value={formData.countryCode}
                        onChange={(e) => handleInputChange("countryCode", e.target.value)}
                        disabled={otpState === "verified"}
                        className="w-32 h-11 px-3 border border-gray-300 rounded-lg bg-white cursor-pointer disabled:bg-gray-50 disabled:cursor-not-allowed appearance-none shadow-sm hover:border-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        style={{
                          backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E")`,
                          backgroundRepeat: 'no-repeat',
                          backgroundPosition: 'right 0.5rem center',
                          backgroundSize: '1.5em 1.5em',
                          paddingRight: '2.5rem'
                        }}
                      >
                        {countryCodes.map((code) => (
                          <option key={code.value} value={code.value}>
                            {code.flag} {code.value}
                          </option>
                        ))}
                      </select>

                      <div className="flex-1 flex gap-3">
                        <Input
                          id="mobile"
                          type="tel"
                          value={formData.mobile}
                          onChange={(e) => handleMobileChange(e.target.value)}
                          placeholder="555 123 4567"
                          disabled={otpState === "verified"}
                          className={`h-11 ${otpState === "verified" ? "bg-gray-50" : ""} border-gray-300 focus:border-emerald-500 focus:ring-emerald-500`}
                          required
                        />

                        {otpState === "idle" || otpState === "error" ? (
                          <Button
                            type="button"
                            onClick={() => void handleSendOTP()}
                            className="bg-emerald-600 hover:bg-emerald-700 whitespace-nowrap h-11 px-6"
                          >
                            <Phone className="w-4 h-4 mr-2" />
                            Send OTP
                          </Button>
                        ) : otpState === "verified" ? (
                          <div className="flex items-center gap-2 px-5 bg-emerald-50 border-2 border-emerald-200 rounded-lg">
                            <CheckCircle className="w-5 h-5 text-emerald-600" />
                            <span className="text-sm text-emerald-700 font-semibold">Verified</span>
                          </div>
                        ) : null}
                      </div>
                    </div>

                    {(otpState === "sent" || otpState === "verifying" || otpState === "error") && (
                      <div className="mt-4 p-6 bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 rounded-xl space-y-4">
                        <div className="flex items-start gap-3">
                          <div className="flex-1">
                            <p className="text-base font-semibold text-gray-900">Enter Verification Code</p>
                            <p className="text-sm text-gray-600 mt-1">
                              We sent a 6-digit code to {formData.countryCode} {formData.mobile}
                            </p>
                          </div>
                          {otpState === "verifying" && <Loader2 className="w-5 h-5 text-emerald-600 animate-spin" />}
                        </div>

                        <div className="flex gap-3 justify-center">
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
                              className={`w-14 h-14 text-center text-xl font-bold ${otpState === "error" ? "border-red-500 bg-red-50" : "border-gray-300"
                                } focus:border-emerald-500 focus:ring-emerald-500`}
                              disabled={otpState === "verifying"}
                            />
                          ))}
                        </div>

                        {otpError && (
                          <div className="flex items-center gap-2 text-red-600 text-sm bg-red-50 p-3 rounded-lg">
                            <AlertCircle className="w-4 h-4" />
                            <span>{otpError}</span>
                          </div>
                        )}

                        <div className="text-center pt-2">
                          {resendTimer > 0 ? (
                            <p className="text-sm text-gray-600">
                              Resend code in <span className="font-bold text-emerald-600">{resendTimer}s</span>
                            </p>
                          ) : (
                            <Button
                              type="button"
                              variant="outline"
                              size="sm"
                              onClick={() => void handleResendOTP()}
                              className="text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 border-emerald-300"
                            >
                              <RefreshCw className="w-4 h-4 mr-2" />
                              Resend OTP
                            </Button>
                          )}
                        </div>
                      </div>
                    )}
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <Label htmlFor="country" className="text-sm font-semibold text-gray-700">
                        Country <span className="text-red-500">*</span>
                      </Label>
                      <select
                        value={formData.country}
                        onChange={(e) => handleInputChange("country", e.target.value)}
                        className="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500"
                        required
                      >
                        <option value="">Select country</option>
                        {countries.map((c) => (
                          <option key={c.value} value={c.value}>
                            {c.label}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="state" className="text-sm font-semibold text-gray-700">State / Province</Label>
                      <Input
                        id="state"
                        value={formData.state}
                        onChange={(e) => handleInputChange("state", e.target.value)}
                        placeholder="Enter state or province"
                        className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                      />
                    </div>
                  </div>
                </div>
              )}

              {/* Step 2: Professional Information */}
              {currentStep === 2 && (
                <div className="space-y-6 animate-in fade-in duration-500">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <Label htmlFor="jobTitle" className="text-sm font-semibold text-gray-700">
                        Job Title <span className="text-red-500">*</span>
                      </Label>
                      <Input
                        id="jobTitle"
                        value={formData.jobTitle}
                        onChange={(e) => handleInputChange("jobTitle", e.target.value)}
                        placeholder="e.g. Senior Engineer"
                        className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                        required
                      />
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="organization" className="text-sm font-semibold text-gray-700">
                        Organization <span className="text-red-500">*</span>
                      </Label>
                      <Input
                        id="organization"
                        value={formData.organization}
                        onChange={(e) => handleInputChange("organization", e.target.value)}
                        placeholder="Company / Organization Name"
                        className="h-11 border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                        required
                      />
                    </div>
                  </div>
                </div>
              )}

              {/* Step 3: Communities & Industry */}
              {currentStep === 3 && (
                <div className="space-y-8 animate-in fade-in duration-500">
                  {/* Communities Section */}
                  <div className="space-y-4">
                    <div className="pb-2 border-b">
                      <Label className="text-base font-bold text-gray-800">
                        Select Communities <span className="text-red-500">*</span>
                      </Label>
                      <p className="text-xs text-gray-500 mt-1">Choose one or more communities relevant to your expertise</p>
                    </div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                      {communities.map((comm) => {
                        const checked = formData.communities.includes(comm.value);
                        return (
                          <label
                            key={comm.value}
                            className={`flex items-center gap-3 rounded-lg px-4 py-3.5 cursor-pointer transition-all border-2 ${checked
                              ? "bg-emerald-50 border-emerald-600 shadow-md"
                              : "bg-white border-gray-200 hover:border-emerald-300 hover:shadow-sm"
                              }`}
                          >
                            <Checkbox
                              checked={checked}
                              onCheckedChange={(v) => {
                                if (v === "indeterminate") return;
                                if (v !== checked) handleToggleCommunity(comm.value);
                              }}
                              className="border-2 data-[state=checked]:bg-emerald-600 data-[state=checked]:border-emerald-600"
                            />
                            <span className="text-sm font-medium text-gray-700">{comm.label}</span>
                          </label>
                        );
                      })}
                    </div>
                  </div>

                  {/* Sub-Communities Section */}
                  {formData.communities.length > 0 && availableSubCommunities.length > 0 && (
                    <div className="space-y-4">
                      <div className="pb-2 border-b">
                        <Label className="text-base font-bold text-gray-800">Sub-Communities</Label>
                        <p className="text-xs text-gray-500 mt-1">Optional: Narrow down your areas of interest</p>
                      </div>
                      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        {availableSubCommunities.map((sub) => {
                          const checked = formData.subCommunities.includes(sub.value);
                          return (
                            <label
                              key={sub.value}
                              className={`flex items-center gap-3 rounded-lg px-4 py-3.5 cursor-pointer transition-all border-2 ${checked
                                ? "bg-blue-50 border-blue-600 shadow-md"
                                : "bg-white border-gray-200 hover:border-blue-300 hover:shadow-sm"
                                }`}
                            >
                              <Checkbox
                                checked={checked}
                                onCheckedChange={(v) => {
                                  if (v === "indeterminate") return;
                                  if (v !== checked) handleToggleSubCommunity(sub.value);
                                }}
                                className="border-2 data-[state=checked]:bg-blue-600 data-[state=checked]:border-blue-600"
                              />
                              <span className="text-sm font-medium text-gray-700">{sub.label}</span>
                            </label>
                          );
                        })}
                      </div>
                    </div>
                  )}

                  {/* Industry & Sub-Industry Section */}
                  <div className="space-y-4 pt-2">
                    <div className="pb-2 border-b">
                      <Label className="text-base font-bold text-gray-800">Industry Classification</Label>
                      <p className="text-xs text-gray-500 mt-1">Select your primary industry and specialization</p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                      {/* Industry Dropdown */}
                      <div className="space-y-2">
                        <Label htmlFor="industry" className="text-sm font-semibold text-gray-700">
                          Industry <span className="text-red-500">*</span>
                        </Label>
                        <select
                          value={formData.industry}
                          onChange={(e) => handleInputChange("industry", e.target.value)}
                          disabled={!formData.communities.length}
                          className="h-12 w-full rounded-lg border-2 border-gray-300 bg-white px-3 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
                        >
                          <option value="">
                            {formData.communities.length ? "Choose your industry" : "Select communities first"}
                          </option>

                          {filteredIndustries.map((ind) => (
                            <option key={ind.value} value={ind.value}>
                              {ind.label}
                            </option>
                          ))}
                        </select>
                        {!formData.communities.length && (
                          <p className="text-xs text-amber-600 flex items-center gap-1 mt-1">
                            <AlertCircle className="w-3 h-3" />
                            Please select at least one community first
                          </p>
                        )}
                      </div>

                      {/* Sub-Industry Dropdown */}
                      <div className="space-y-2">
                        <Label htmlFor="subIndustry" className="text-sm font-semibold text-gray-700">
                          Sub-Industry
                        </Label>
                        <select
                          value={formData.subIndustry}
                          onChange={(e) => handleInputChange("subIndustry", e.target.value)}
                          disabled={!formData.industry}
                          className="h-12 w-full rounded-lg border-2 border-gray-300 bg-white px-3 shadow-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
                        >
                          <option value="">
                            {!formData.industry ? "Select industry first" : "Choose specialization"}
                          </option>

                          {subIndustryOptions.map((sub) => (
                            <option key={sub.value} value={sub.value}>
                              {sub.label}
                            </option>
                          ))}
                        </select>
                        {!safeSubIndustryMap[formData.industry]?.length && formData.industry && (
                          <p className="text-xs text-gray-500 italic mt-1">
                            Showing common specializations for {formData.industry}
                          </p>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              )}

              {/* Navigation Buttons */}
              <div className="flex items-center justify-between mt-8 pt-6 border-t">
                {currentStep > 1 ? (
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => setCurrentStep(currentStep - 1)}
                    className="px-6 h-11"
                  >
                    <ChevronLeft className="w-4 h-4 mr-2" />
                    Previous
                  </Button>
                ) : (
                  <div />
                )}

                {currentStep < 3 ? (
                  <Button
                    type="button"
                    onClick={() => setCurrentStep(currentStep + 1)}
                    disabled={
                      (currentStep === 1 && !isStep1Valid) ||
                      (currentStep === 2 && !isStep2Valid)
                    }
                    className="bg-emerald-600 hover:bg-emerald-700 px-6 h-11 ml-auto"
                  >
                    Next
                    <ChevronRight className="w-4 h-4 ml-2" />
                  </Button>
                ) : (
                  <Button
                    type="submit"
                    className="bg-emerald-600 hover:bg-emerald-700 px-8 h-11 ml-auto"
                    disabled={isLoading || !isStep3Valid}
                  >
                    {isLoading ? (
                      <>
                        <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                        Creating Profile...
                      </>
                    ) : (
                      <>
                        <CheckCircle className="w-4 h-4 mr-2" />
                        Complete Registration
                      </>
                    )}
                  </Button>
                )}
              </div>

              {otpError && currentStep === 3 && (
                <div className="flex items-center gap-2 text-red-600 text-sm bg-red-50 p-4 rounded-lg mt-4">
                  <AlertCircle className="w-5 h-5" />
                  <span>{otpError}</span>
                </div>
              )}
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
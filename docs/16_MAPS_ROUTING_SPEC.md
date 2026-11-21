# 16 – Maps & Routing Specification

## 1. Separate capabilities
- Map renderer: draws basemap, markers and lines.
- Place search: finds points of interest.
- Geocoding: converts addresses/places to coordinates.
- Routing: calculates navigable paths, distance and duration by mode.
- Place/content data: names, categories, opening hours, photos and provenance.

MapLibre is a possible renderer, not a complete place-search or routing provider.

## 2. User experience
- Map and itinerary list stay synchronized.
- Selecting a stop highlights its marker and corresponding card.
- Filter/switch by day.
- Route legs connect consecutive stops only when route data exists.
- "Chỉ đường" opens Google Maps for the selected stop.
- Itinerary route card connector is a solid line + motorcycle icon + estimated time/distance when supported.
- Clearly label route metrics as estimates.
- Never represent a straight line between coordinates as a road route.
- Outside Da Nang–Hoi An show the pilot-area warning.

## 3. Data contract
A route leg should include origin/destination IDs, travel mode, distance_meters, duration_seconds, provider, calculated_at, status, geometry/reference and warnings. A place should include provider ID, coordinates, source, retrieved_at and verification status.

## 4. Validation
- Coordinates must be in valid ranges and tied to resolved places.
- Check route provider response and geometry validity.
- Detect missing route, excessive distance/time, schedule overlap and unavailable travel mode.
- Do not infer travel time from distance alone.
- If routing fails, show "chưa có dữ liệu tuyến đường" and keep itinerary usable.
- Cache provider results with bounded TTL and attribution/license compliance.

## 5. Provider selection criteria
Vietnam coverage, motorcycle routing support, accuracy, traffic/time estimates, licensing, attribution, quota, cost, latency, privacy, caching terms and fallback. No provider has been selected yet.

## 6. Test cases
Known Da Nang–Hoi An route; same-place route; missing coordinates; provider timeout; invalid geometry; no route result; route mode unsupported; schedule too tight; stale cached route; location outside pilot.

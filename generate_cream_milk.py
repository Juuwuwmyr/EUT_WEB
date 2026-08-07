"""
Generate individual Cream Milk Series images using Pollinations.AI (FREE, no API key)
"""
import io, time, sys, urllib.request, urllib.parse
from pathlib import Path

try:
    from PIL import Image
except ImportError:
    import os; os.system(f"{sys.executable} -m pip install Pillow -q")
    from PIL import Image

OUTPUT_DIR = Path(r"c:\Users\alex m cortez\Desktop\EUT_WEB\public\images\menu")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

POLLINATIONS_URL = "https://image.pollinations.ai/prompt/{prompt}?width=1024&height=1024&model=flux-pro&nologo=true&enhance=false&seed={seed}"

BASE = "Professional Filipino milk tea drink photography, restaurant menu style, dark moody background, studio lighting, 4K, appetizing, no text, no watermark. "

ITEMS = [
    ("taro-cream-milk",         BASE + "Taro cream milk drink in clear cup, purple-lavender color, creamy swirls, condensation on cup"),
    ("melon-cream-milk",        BASE + "Melon cream milk drink in clear cup, pale green color, creamy, refreshing, condensation"),
    ("java-chip-cream-milk",    BASE + "Java chip vanilla cream milk drink in clear cup, caramel-brown color with chocolate chip bits"),
    ("mango-cheesecake-cream-milk", BASE + "Mango cheesecake cream milk drink in clear cup, yellow-orange color, creamy swirls"),
    ("okinawa-cream-milk",      BASE + "Okinawa brown sugar cream milk drink in clear cup, dark caramel color, brown sugar drizzle"),
    ("strawberry-cream-milk",   BASE + "Strawberry cream milk drink in clear cup, pink-red color, creamy, strawberry syrup swirls"),
    ("matcha-cream-milk",       BASE + "Matcha green tea cream milk drink in clear cup, vibrant green color, creamy top"),
    ("chocolate-cream-milk",    BASE + "Chocolate cream milk drink in clear cup, rich dark brown color, chocolate drizzle, creamy"),
    ("salted-caramel-cream-milk", BASE + "Salted caramel cream milk drink in clear cup, golden caramel color, caramel swirls, sea salt"),
    ("vanilla-cream-milk",      BASE + "Classic vanilla cream milk drink in clear cup, pale cream-white color, smooth creamy texture"),
    ("cookies-cream-milk",      BASE + "Cookies and cream milk drink in clear cup, white with dark cookie crumbles, creamy"),
    ("milk-chocolate-cream-milk", BASE + "Milk chocolate cream drink in clear cup, light brown color, creamy smooth, chocolate swirls"),
]

def generate(filename, prompt, seed=42):
    out = OUTPUT_DIR / f"{filename}.webp"
    if out.exists():
        print(f"  Skip {filename} (exists)")
        return True
    print(f"  Generating {filename}...", end=" ", flush=True)
    try:
        url = POLLINATIONS_URL.format(
            prompt=urllib.parse.quote(prompt),
            seed=seed
        )
        req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
        with urllib.request.urlopen(req, timeout=60) as r:
            data = r.read()
        img = Image.open(io.BytesIO(data)).convert("RGB")
        img.save(out, "WEBP", quality=88, method=6)
        kb = out.stat().st_size // 1024
        print(f"OK {kb}KB")
        return True
    except Exception as e:
        print(f"FAILED - {e}")
        return False

ok, fail = 0, []
for i, (filename, prompt) in enumerate(ITEMS):
    if generate(filename, prompt, seed=100 + i):
        ok += 1
    else:
        fail.append(filename)
    time.sleep(3)

print(f"\nDone: {ok}/{len(ITEMS)}")
if fail:
    print(f"Failed: {', '.join(fail)}")

print("\n-- SQL to run in phpMyAdmin --")
name_map = {
    "taro-cream-milk":              "Taro Cream Milk",
    "melon-cream-milk":             "Melon Cream Milk",
    "java-chip-cream-milk":         "Java Chip Vanilla",
    "mango-cheesecake-cream-milk":  "Mango Cheesecake",
    "okinawa-cream-milk":           "Okinawa Cream Milk",
    "strawberry-cream-milk":        "Strawberry Cream Milk",
    "matcha-cream-milk":            "Matcha Cream Milk",
    "chocolate-cream-milk":         "Chocolate Cream Milk",
    "salted-caramel-cream-milk":    "Salted Caramel",
    "vanilla-cream-milk":           "Vanilla Cream Milk",
    "cookies-cream-milk":           "Cookies & Cream",
    "milk-chocolate-cream-milk":    "Milk Chocolate",
}
for filename, name in name_map.items():
    print(f"UPDATE menu_items SET image='/images/menu/{filename}.webp' WHERE name='{name}';")

"""
EUT Snack House - Menu Image Generator
Generates AI food images using Google Gemini (Imagen 3) then converts to WebP.
Uses the real EUT seed SQL menu items (NOT eut_restaurant.sql).

Usage:  python generate_menu_images.py
"""
import os, io, time, sys
from pathlib import Path

try:
    from google import genai
    from google.genai import types
except ImportError:
    os.system(f"{sys.executable} -m pip install google-genai -q")
    from google import genai
    from google.genai import types

try:
    from PIL import Image
except ImportError:
    os.system(f"{sys.executable} -m pip install Pillow -q")
    from PIL import Image

API_KEY    = "AIzaSyCW7RGrWBeXN12zZQtSTu-QLult765CX6Q"
OUTPUT_DIR = Path(r"c:\wamp64\www\EUT_WEB\public\images\menu")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

BASE = (
    "Professional Filipino food photography, restaurant menu style, "
    "dark moody background, studio lighting, 4K, appetizing, no text, no watermark. "
)

# (id, filename, prompt)  — one representative image per menu item
MENU_ITEMS = [
    # ── Unlimited ──────────────────────────────────────────────────────────────
    (20, "pork-inasal",           BASE+"Grilled pork inasal on banana leaf, basted with annatto marinade, unli rice on side, calamansi"),
    (21, "chicken-inasal",        BASE+"Grilled chicken inasal on banana leaf, charred skin, annatto baste, rice and calamansi"),
    (22, "unli-3pcs-wings",       BASE+"3 pieces crispy fried chicken wings on dark plate, golden brown, dipping sauce"),
    (23, "unli-5pcs-wings",       BASE+"5 pieces crispy fried chicken wings piled on dark slate, golden crispy"),

    # ── Sagana Package ─────────────────────────────────────────────────────────
    (24, "sagana-sa-eut",         BASE+"Grand Filipino boodle feast spread on banana leaf: sinigang, kare-kare, crispy pata, bangus, rice, overhead shot"),
    (25, "sigad-sa-eut",          BASE+"Filipino feast for 4-6 persons: sinigang, crispy pata, chopsuey, pork sisig, lumpia shanghai, overhead banana leaf"),
    (26, "sawa-sa-eut",           BASE+"Filipino feast spread for 5-7 persons on banana leaf: sinigang, kare-kare, crispy pata, bangus, rice, overhead"),
    (27, "sabik-sa-eut",          BASE+"Party platter for 3-5 persons: nachos, chicken wings, pasta, burger, fries, drinks on wooden table overhead"),

    # ── Sweets Corner ──────────────────────────────────────────────────────────
    (28, "classic-halo-halo",     BASE+"Classic Filipino halo-halo in tall glass: shaved ice, ube, leche flan, beans, milk, colorful layers"),
    (29, "special-halo-halo",     BASE+"Special Filipino halo-halo with premium toppings, ube ice cream, leche flan, shaved ice, vibrant"),
    (30, "halo-halong-eut",       BASE+"Ultimate EUT signature halo-halo, overflowing glass, ube, leche flan, premium toppings, dramatic lighting"),
    (31, "leche-flan",            BASE+"Creamy Filipino leche flan on white plate, golden caramel top, smooth custard surface"),
    (32, "mais-con-yelo",         BASE+"Filipino mais con yelo: sweet corn kernels, shaved ice, evaporated milk in glass bowl"),
    (33, "mango-tappioca",        BASE+"Fresh mango chunks with tapioca pearls and coconut milk in clear glass, tropical"),
    (34, "buko-pandan",           BASE+"Filipino buko pandan dessert: young coconut strips, green pandan jelly, cream, in clear bowl"),
    (35, "saging-con-yelo",       BASE+"Banana slices with shaved ice and sweet milk in glass bowl, Filipino dessert"),
    (36, "vegetable-salad",       BASE+"Fresh colorful Filipino vegetable salad in white bowl, mixed greens, carrots, with dressing"),

    # ── Cream Milk Series ──────────────────────────────────────────────────────
    (37, "cream-milk-series",     BASE+"Assortment of colorful cream milk drinks in cups: taro purple, matcha green, chocolate brown, strawberry pink, overhead"),

    # ── Fruit Tea ──────────────────────────────────────────────────────────────
    (49, "fruit-tea",             BASE+"Colorful fruit tea drinks in clear cups with fruit garnish: strawberry, mango, lychee, condensation"),

    # ── Milk Tea ───────────────────────────────────────────────────────────────
    (50, "milk-tea",              BASE+"Premium milk tea in clear cup with tapioca pearls at bottom, brown sugar swirl, wide straw"),

    # ── Iced Coffee Latte ──────────────────────────────────────────────────────
    (51, "iced-coffee-latte",     BASE+"Iced coffee latte in tall clear glass: espresso layers, milk, ice cubes, condensation, caramel drizzle"),

    # ── Iced Coffee Macchiato ──────────────────────────────────────────────────
    (52, "iced-coffee-macchiato", BASE+"Iced coffee macchiato in clear glass: espresso shot over milk and ice, layered effect, beautiful"),

    # ── Signature Shakes ───────────────────────────────────────────────────────
    (53, "strawberry-choco-shake",BASE+"Thick strawberry chocolate milkshake in mason jar, whipped cream, strawberry on top, rich pink-brown"),
    (54, "dark-choco-shake",      BASE+"Rich dark chocolate milkshake in glass, whipped cream, chocolate drizzle, dark moody"),
    (55, "oreo-choco-shake",      BASE+"Creamy Oreo chocolate milkshake in glass, crushed Oreo topping, whipped cream, cookies"),
    (56, "white-choco-shake",     BASE+"Smooth white chocolate milkshake in clear glass, whipped cream, white chocolate shavings"),
    (57, "nutella-choco-shake",   BASE+"Indulgent Nutella chocolate milkshake, Nutella swirls on glass, whipped cream, hazelnuts"),
    (58, "mango-graham-shake",    BASE+"Mango graham milkshake: creamy yellow mango, crushed graham crackers, whipped cream, tall glass"),
    (59, "pb-choco-shake",        BASE+"Peanut butter chocolate milkshake, swirled peanut butter, chocolate, creamy thick"),
    (60, "chocolate-shake",       BASE+"Classic rich chocolate milkshake in glass, whipped cream, chocolate syrup drizzle"),
    (61, "matcha-shake",          BASE+"Premium Japanese matcha milkshake, vibrant green, whipped cream, matcha powder dusted"),
    (62, "biscoff-shake",         BASE+"Creamy Biscoff milkshake, crushed Biscoff cookies, caramel color, whipped cream"),

    # ── Fruit Shakes ───────────────────────────────────────────────────────────
    (63, "mango-shake",           BASE+"Thick fresh mango shake in tall glass, vibrant yellow-orange, mango chunks garnish"),
    (64, "avocado-shake",         BASE+"Creamy avocado shake in glass, thick green color, condensation"),
    (65, "banana-shake",          BASE+"Sweet creamy banana milkshake in glass, pale yellow, banana slice garnish"),
    (66, "melon-shake",           BASE+"Refreshing fresh melon shake, pale green color, melon cube garnish, clear glass"),
    (67, "watermelon-shake",      BASE+"Vibrant red watermelon shake in glass, watermelon slice garnish, refreshing"),
    (68, "apple-shake",           BASE+"Fresh apple shake in glass, pale golden color, apple slice garnish"),
    (69, "dragon-fruit-shake",    BASE+"Exotic dragon fruit shake, vibrant pink-magenta color, dragon fruit garnish"),
    (70, "cucumber-shake",        BASE+"Cool refreshing cucumber shake, pale green, cucumber slice garnish"),
    (71, "strawberry-shake",      BASE+"Sweet strawberry shake in glass, bright red-pink, fresh strawberry garnish"),

    # ── Fruit Shakes / Drinks ──────────────────────────────────────────────────
    (72, "cucumber-lemonade",     BASE+"Refreshing cucumber lemonade in tall glass: cucumber slices, lemon, mint, ice"),
    (73, "red-ice-tea",           BASE+"Deep red iced tea in tall glass with ice cubes, red hibiscus color, lemon"),
    (74, "ice-tea",               BASE+"Classic brewed iced tea in tall glass, amber color, lemon slice, ice"),
    (75, "blue-lemonade",         BASE+"Striking blue lemonade in clear glass, vivid blue, lemon garnish, ice"),
    (76, "black-gulaman",         BASE+"Filipino black gulaman drink in glass, dark jelly cubes, sweet syrup, ice"),
    (77, "strawberry-lemonade",   BASE+"Fresh strawberry lemonade in mason jar: pink-red, strawberry slices, lemon, ice"),
    (78, "calamansi-lemonade",    BASE+"Tangy Filipino calamansi lemonade in glass, yellow-green, calamansi garnish, ice"),
    (79, "lychee-lemonade",       BASE+"Floral lychee lemonade in tall glass, pale pink, lychee fruit, ice"),
    (80, "orange-lemonade",       BASE+"Zesty orange lemonade in glass, bright orange, orange slice, ice cubes"),
    (81, "lemon-ade",             BASE+"Classic fresh lemonade in glass, pale yellow, lemon wheel, ice"),
    (82, "yakult-lemonade",       BASE+"Probiotic Yakult lemonade in glass, creamy white-yellow, Yakult bottle beside"),
    (83, "pineapple-juice",       BASE+"Fresh pineapple juice in tall glass, golden yellow, pineapple slice, ice"),
    (84, "four-seasons",          BASE+"Four Seasons mixed juice blend in glass, colorful layers, tropical fruits garnish"),

    # ── Bilao-Kan ──────────────────────────────────────────────────────────────
    (85, "bilao-kan",             BASE+"Large round bilao tray filled with Filipino pancit: miki noodles, toppings, overhead view, feast size"),

    # ── Pasta and Pancit ───────────────────────────────────────────────────────
    (91, "miki-guisado",          BASE+"Filipino miki guisado: stir-fried thick egg noodles with vegetables and meat, with slice bread"),
    (92, "bihon-guisado",         BASE+"Filipino bihon guisado: stir-fried rice vermicelli with vegetables, with slice bread"),
    (93, "canton-guisado",        BASE+"Filipino canton guisado: stir-fried yellow egg noodles with vegetables, with slice bread"),
    (98, "palabok",               BASE+"Filipino palabok: rice noodles with orange shrimp sauce, chicharron, egg, topped, with slice bread"),
    (99, "lomi-small",            BASE+"Filipino lomi: thick egg noodle soup in bowl, small serving, thick broth, with slice bread"),
    (100,"lomi-medium",           BASE+"Filipino lomi: thick egg noodle soup, medium serving, rich broth, egg, with slice bread"),
    (101,"lomi-large",            BASE+"Filipino lomi: large hearty bowl of thick egg noodle soup, rich toppings"),
    (102,"seafood-pancit",        BASE+"Filipino seafood pancit guisado: noodles with shrimp, squid, vegetables, with slice bread"),
    (103,"spaghetti",             BASE+"Filipino-style spaghetti: sweet tomato sauce, hotdog slices, ground meat, with slice bread"),
    (104,"ham-tuna-pasta",        BASE+"Creamy ham and tuna pasta in white sauce, pasta bowl, with slice bread"),
    (105,"seafood-pasta",         BASE+"Seafood pasta: shrimp, squid, mussels in garlic white wine sauce, with slice bread"),
    (106,"carbonara",             BASE+"Filipino-style carbonara: creamy white sauce, bacon bits, pasta, with slice bread"),

    # ── EUT Sandwich ───────────────────────────────────────────────────────────
    (107,"hotdog-sandwich",       BASE+"Classic Filipino hotdog sandwich on toasted white bread, ketchup, mustard"),
    (108,"ham-tuna-sandwich",     BASE+"Ham and tuna sandwich on toasted bread, creamy filling, lettuce"),
    (109,"ham-egg-sandwich",      BASE+"Ham and fried egg sandwich on toasted bread, melted cheese"),
    (110,"ham-bacon-sandwich",    BASE+"Loaded ham and crispy bacon sandwich, toasted bread, lettuce, tomato"),
    (111,"clubhouse-sandwich",    BASE+"Triple decker clubhouse sandwich: ham, egg, vegetables, toothpick, toasted"),
    (112,"tuna-sandwich",         BASE+"Classic tuna sandwich on toasted white bread, creamy tuna filling, lettuce"),
    (117,"crispy-chicken-sandwich",BASE+"Crunchy crispy fried chicken sandwich, toasted bun, lettuce, sauce dripping"),
    (118,"shawarma-chicken-sandwich",BASE+"Middle Eastern chicken shawarma wrap/sandwich, garlic sauce, vegetables, pita"),
    (119,"grilled-mozzarella",    BASE+"Warm grilled mozzarella cheese sandwich, golden toasted bread, melted cheese pull"),
    (120,"seafood-sandwich",      BASE+"Fresh seafood sandwich on toasted bread, shrimp and fish filling"),

    # ── EUT Giant Burger ───────────────────────────────────────────────────────
    (121,"giant-mozzarella-burger",BASE+"Oversized giant burger with melted mozzarella cheese pull, giant beef patty, dramatic"),
    (122,"giant-seafood-burger",  BASE+"Giant oversized seafood burger patty, fish/shrimp, fresh toppings, towering burger"),
    (123,"aloha-giant-seafood",   BASE+"Tropical Aloha giant seafood burger, pineapple ring, seafood patty, oversized"),
    (124,"aloha-giant-mozza",     BASE+"Tropical Aloha giant burger, pineapple, mozzarella cheese, oversized dramatic"),
    (125,"eut-giant-signature",   BASE+"Ultimate EUT signature giant burger, stacked tall, special sauce dripping, dramatic"),

    # ── Snacks ─────────────────────────────────────────────────────────────────
    (126,"classic-fries",         BASE+"Golden crispy classic salted french fries in paper cone, sea salt visible"),
    (127,"couple-fries",          BASE+"Bigger serving of crispy golden fries in basket for two, sea salt"),
    (128,"family-fries",          BASE+"Large family-sized serving of golden crispy fries, overflowing basket"),
    (129,"special-fries",         BASE+"Seasoned loaded fries with cheese sauce drizzle, garlic parmesan coating, golden"),
    (130,"classic-nachos",        BASE+"Crunchy tortilla nachos chips with classic cheese dip in bowl"),
    (131,"special-nachos",        BASE+"Loaded special nachos: chips, melted cheese, jalapeños, sour cream, guacamole"),
    (132,"nachos-fries",          BASE+"Ultimate combo: nachos AND fries together in one platter, cheese sauce, toppings"),
    (133,"bread-roll",            BASE+"Soft freshly baked bread roll on plate, golden crust, Filipino bakery style"),
    (134,"tuna-bread-roll",       BASE+"Soft bread roll stuffed with savory tuna filling, baked golden"),
    (135,"siomai-big",            BASE+"Big Filipino siomai dumplings on plate, steamed or fried, soy sauce, calamansi"),
    (136,"siomai-small",          BASE+"Small Filipino siomai dumplings on small plate, soy sauce, calamansi garnish"),
    (137,"beef-shawarma",         BASE+"Juicy beef shawarma wrap: pita bread, beef strips, garlic sauce, vegetables"),
    (138,"chicken-shawarma",      BASE+"Tender chicken shawarma wrap, pita bread, garlic sauce, fresh vegetables, wrap"),
    (139,"beef-quesadillas",      BASE+"Crispy beef quesadilla cut in triangles, melted cheese, seasoned beef filling"),
    (140,"cheese-quesadillas",    BASE+"Golden crispy cheese quesadilla, melted mozzarella pull, cut in triangles"),
    (141,"beef-burrito",          BASE+"Hearty beef burrito wrap: rice, beans, beef, salsa, sour cream, wrapped in tortilla"),
    (142,"pork-burrito",          BASE+"Savory pork burrito wrap, rice, pork filling, wrapped in flour tortilla"),

    # ── Hiwa Hiwalay / Solo Order ───────────────────────────────────────────────
    (143,"sinigang-baboy",        BASE+"Filipino sinigang na baboy: pork ribs in sour tamarind broth, vegetables, steam"),
    (144,"sinigang-hipon",        BASE+"Filipino sinigang na hipon: whole shrimp in tamarind broth, kangkong, vegetables"),
    (145,"sinigang-bangus",       BASE+"Filipino sinigang na bangus: milkfish in sour tamarind soup, vegetables, steam"),
    (146,"chopsuey",              BASE+"Filipino chopsuey: stir-fried mixed vegetables, quail eggs, in wok-style dish"),
    (147,"pinakbet",              BASE+"Filipino pinakbet: mixed vegetables with bagoong shrimp paste, sitaw, ampalaya"),
    (148,"pork-binagoongan",      BASE+"Filipino pork binagoongan: crispy pork belly cooked in bagoong alamang shrimp paste"),
    (149,"pork-bicol-express",    BASE+"Filipino pork Bicol express: pork in spicy coconut milk, red chilies, bagoong"),
    (150,"bangus-bicol-express",  BASE+"Filipino bangus Bicol express: milkfish in spicy coconut milk sauce"),
    (151,"lumpia-shanghai",       BASE+"Filipino lumpia Shanghai: crispy golden fried pork spring rolls, dipping sauce"),
    (152,"beef-kare-kare",        BASE+"Filipino beef kare-kare: beef in thick peanut sauce, vegetables, bagoong on side"),
    (153,"crispy-pork-kare-kare", BASE+"Filipino crispy pork kare-kare: crispy pork belly in rich peanut sauce"),
    (154,"fish-fillet",           BASE+"Crispy fish fillet with sweet and sour sauce, plated with garnish"),
    (155,"pork-bbq",              BASE+"Filipino pork BBQ skewers: grilled on stick, caramelized, charred edges, calamansi"),
    (156,"sizzling-tofu",         BASE+"Filipino sizzling tofu: crispy tofu on sizzling plate with soy sauce, egg, vegetables"),
    (157,"bulalo",                BASE+"Filipino bulalo: beef shank and bone marrow soup, clear broth, corn, bokchoy, steam"),
    (158,"pork-sisig",            BASE+"Filipino pork sisig: sizzling chopped pork on cast iron plate, calamansi, chili"),
    (159,"crispy-pata",           BASE+"Filipino crispy pata: deep-fried whole pork leg, golden crackling skin, vinegar dip"),

    # ── EUT Pak-Pak Wings ──────────────────────────────────────────────────────
    (160,"wings-3pcs",            BASE+"3 pieces crispy glazed chicken wings on dark slate, sweet chili sauce, garnish"),
    (161,"wings-6pcs",            BASE+"6 pieces sauced chicken wings on wooden board, mixed flavors, dipping sauce"),
    (162,"wings-9pcs",            BASE+"9 pieces crispy chicken wings on plate, assorted flavors, Buffalo and BBQ sauces"),
    (163,"wings-12pcs",           BASE+"12 pieces crispy chicken wings on large platter, multiple sauces, party size"),
    (164,"wings-24pcs",           BASE+"24 pieces chicken wings tower on large platter, party feast, assorted glazed flavors"),

    # ── EUT Sa Hita Drumsticks ─────────────────────────────────────────────────
    (165,"hita-3pcs",             BASE+"3 pieces crispy fried chicken drumsticks on plate, golden brown, dipping sauce"),
    (166,"hita-6pcs",             BASE+"6 pieces fried chicken drumsticks on wooden board, crispy golden skin"),
    (167,"hita-9pcs",             BASE+"9 pieces crispy chicken drumsticks piled on plate, Filipino style"),
    (168,"hita-12pcs",            BASE+"12 pieces crispy chicken drumsticks on large platter, party serving"),
    (169,"hita-24pcs",            BASE+"24 pieces crispy fried chicken drumsticks feast platter, party size"),

    # ── Rice Bowl ──────────────────────────────────────────────────────────────
    (170,"rice-bowl",             BASE+"Filipino rice bowl with toppings: nuggets/ham/egg on steamed white rice, in bowl"),
    (171,"fried-chicken-rice-bowl",BASE+"Crispy fried chicken leg on steamed rice in bowl, BBQ sauce drizzle"),
    (172,"fried-pork-rice-bowl",  BASE+"Crispy fried pork on steamed rice in bowl, soy garlic sauce"),

    # ── EUT Sex Combo ──────────────────────────────────────────────────────────
    (173,"sex-combo",             BASE+"Filipino sinangag breakfast combo: garlic fried rice, egg, mixed viands (sisig, hotdog, bacon) on plate with drinks"),

    # ── EUT Giant Burger (remaining) ───────────────────────────────────────────
    (186,"sex-combo-plate",       BASE+"Filipino sinangag + egg + fillet + Hungarian sausage combo plate with drinks"),
]


def generate_and_save(client, item_id, filename, prompt):
    out_path = OUTPUT_DIR / f"{filename}.webp"
    if out_path.exists():
        print(f"  ⏭  Skip #{item_id} {filename} (exists)")
        return True
    print(f"  🎨 #{item_id} {filename} ...", end=" ", flush=True)
    try:
        response = client.models.generate_content(
            model="imagen-3.0-generate-002",
            contents=prompt,
            config=types.GenerateContentConfig(
                response_modalities=["IMAGE"],
                number_of_images=1,
            ),
        )
        # Extract image bytes from response
        img_bytes = None
        for part in response.candidates[0].content.parts:
            if hasattr(part, 'inline_data') and part.inline_data:
                img_bytes = part.inline_data.data
                break
        if not img_bytes:
            print("❌ No image in response")
            return False
        img = Image.open(io.BytesIO(img_bytes)).convert("RGB")
        img.save(out_path, "WEBP", quality=88, method=6)
        kb = out_path.stat().st_size // 1024
        print(f"✅ {kb}KB")
        return True
    except Exception as e:
        print(f"❌ {e}")
        return False


def print_sql():
    print("\n" + "="*60)
    print("Run these SQL statements in phpMyAdmin:")
    print("="*60)
    done = set()
    for item_id, filename, _ in MENU_ITEMS:
        if item_id not in done:
            print(f"UPDATE menu_items SET image='/images/menu/{filename}.webp' WHERE id={item_id};")
            done.add(item_id)
    print("="*60)


def main():
    global API_KEY
    if API_KEY == "PASTE_YOUR_NEW_KEY_HERE" or not API_KEY:
        print("="*60)
        print("  EUT Snack House — Menu Image Generator")
        print("  Get FREE key: https://aistudio.google.com/apikey")
        print("="*60)
        API_KEY = input("  Paste your Gemini API key: ").strip()
    if not API_KEY:
        print("No key provided."); sys.exit(1)

    client = genai.Client(api_key=API_KEY)
    print(f"\n📁 Output: {OUTPUT_DIR}")
    print(f"🖼  Items:  {len(MENU_ITEMS)}\n")

    ok, fail = 0, []
    for item_id, filename, prompt in MENU_ITEMS:
        if generate_and_save(client, item_id, filename, prompt):
            ok += 1
        else:
            fail.append(filename)
        time.sleep(2)   # respect rate limit

    print(f"\n✅ Done: {ok}/{len(MENU_ITEMS)}")
    if fail:
        print(f"❌ Failed: {', '.join(fail)}")
    print_sql()

if __name__ == "__main__":
    main()

# Rebuilding the brand assets

Everything in `public/images/brand/` and the three favicons are generated from
`powerstik-logo-master.png` in this folder. To redo them (needs Python with Pillow):

```python
from PIL import Image

SRC  = 'docs/design/brand/powerstik-logo-master.png'
INK  = (34, 31, 32)     # #221F20
LIME = (199, 255, 0)    # #C7FF00

master = Image.open(SRC).convert('RGBA')
master = master.crop(master.getbbox())          # 1183 x 267 after trimming

def recolour(img, frm, to, tol=90):
    """Swap one flat colour for another, keeping the anti-aliased alpha."""
    out = img.copy(); px = out.load()
    for y in range(out.height):
        for x in range(out.width):
            r, g, b, a = px[x, y]
            if a and abs(r-frm[0]) + abs(g-frm[1]) + abs(b-frm[2]) < tol:
                px[x, y] = (*to, a)
    return out

def fit(img, w):
    return img.resize((w, round(img.height * w / img.width)), Image.LANCZOS)

fit(master, 960).save('public/images/brand/logo.png')
fit(recolour(master, INK, (255, 255, 255)), 960).save('public/images/brand/logo-white.png')

# The mark: first glyph ("p"), ink on a lime tile with 20% padding
p = master.crop((0, 0, 136, master.height)); p = p.crop(p.getbbox())

def tile(size):
    pad = round(size * .20); box = size - pad * 2
    s = min(box / p.width, box / p.height)
    art = p.resize((round(p.width * s), round(p.height * s)), Image.LANCZOS)
    c = Image.new('RGBA', (size, size), (*LIME, 255))
    c.alpha_composite(art, ((size - art.width) // 2, (size - art.height) // 2))
    return c

tile(512).save('public/images/brand/mark.png')
tile(180).convert('RGB').save('public/apple-touch-icon.png')
tile(32).convert('RGB').save('public/favicon-32.png')
tile(64).save('public/favicon.ico', sizes=[(16, 16), (32, 32), (48, 48)])
```

Replace this whole step the moment the client sends vector artwork.

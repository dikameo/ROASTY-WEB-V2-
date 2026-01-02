import csv

csv_file = 'database/data/products.csv'
rows = []

with open(csv_file, 'r', encoding='utf-8') as f:
    reader = csv.reader(f)
    header = next(reader)
    rows.append(header)

    for row in reader:
        # Index 5 is image_urls
        if len(row) > 5:
            image_urls = row[5].strip()
            if image_urls == '[]' or image_urls == '':
                product_name = row[0].split()[0]  # First word of product name
                data_uri = f'data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 400 400%27%3E%3Crect fill=%278B4513%27 width=%27400%27 height=%27400%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 dominant-baseline=%27middle%27 text-anchor=%27middle%27 font-size=%2724%27 fill=%27%23FFF%27 font-family=%27Arial%27%3E{product_name}%3C/text%3E%3C/svg%3E'
                row[5] = f'["{data_uri}"]'
        rows.append(row)

with open(csv_file, 'w', encoding='utf-8', newline='') as f:
    writer = csv.writer(f)
    writer.writerows(rows)

print('✅ CSV updated with data URIs')

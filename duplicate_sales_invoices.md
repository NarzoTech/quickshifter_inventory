# Duplicate Sales Invoices Report

**Generated:** 2026-02-28
**Database:** bikersfu_inventory.sql

- **Total Sales Records:** 394
- **Total Unique Invoice Numbers:** 224
- **Total Duplicate Invoice Numbers:** 168

## Summary

| Category              | Count | Description                                                                        |
| --------------------- | ----- | ---------------------------------------------------------------------------------- |
| Invoice Number Reset  | 167   | Same INV-X used in both old period (Sale ID < 184) and new period (Sale ID >= 184) |
| Genuine Duplicates    | 0     | Same invoice within the same period - corrections/adjustments                      |
| Non-Standard Invoices | 1     | Invoice numbers like '1', '174' instead of 'INV-X' format                          |

---

## 1. Non-Standard Invoice Numbers

These sales have invoice numbers that don't follow the `INV-X` format.

### Invoice: `1` (4 occurrences)

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid     | Due     | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | -------- | ------- | ------ |
| 180     | 553         | rudro R15m -Nabil Vai | 2025-12-30 | 2000        | 2000.00  | 0.00    | ACTIVE |
| 181     | 0           | Walk-in/Guest         | 2025-12-31 | 10500       | 10500.00 | 0.00    | ACTIVE |
| 182     | 520         | Sazal Vai             | 2025-12-31 | 1150        | 0.00     | 1150.00 | ACTIVE |
| 183     | 554         | himu vai- pulsar -    | 2025-12-19 | 10000       | 10000.00 | 0.00    | ACTIVE |

**Customer Dues:**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 85     | 520         | Sazal Vai     | 1150.00    | 0.00        | ACTIVE |

---

## 2. Genuine Duplicate Invoices (Same Period)

These invoices appear multiple times within the same period, likely due to corrections, adjustments, or data entry errors.

---

## 3. Invoice Number Reset Duplicates

The invoice numbering was **reset** around Sale ID 184 (approximately January 2026). This caused all invoice numbers from `INV-1` onwards to be reused.
Below are all affected invoices with their old and new period records.

| Invoice | Old Sale ID | Old Customer               | Old Grand Total | Old Due   | New Sale ID | New Customer                         | New Grand Total | New Due  |
| ------- | ----------- | -------------------------- | --------------- | --------- | ----------- | ------------------------------------ | --------------- | -------- |
| INV-1   | 1           | Walk-in                    | 1630            | 0.00      | 184         | Walk-in                              | 11930           | 0.00     |
| INV-2   | 2           | Hamim Vai                  | 1200            | 0.00      | 185         | Shovon Vai - NX - 200                | 5800            | 0.00     |
| INV-3   | 3           | Walk-in                    | 6550            | 0.00      | 186         | Walk-in                              | 1450            | 0.00     |
| INV-4   | 4           | Walk-in                    | 17380           | 0.00      | 187         | Sazal Vai                            | 800             | 0.00     |
| INV-5   | 5           | Walk-in                    | 1000            | 0.00      | 188         | Walk-in                              | 2620            | 0.00     |
| INV-6   | 6           | Walk-in                    | 2280            | 0.00      | 189         | Walk-in                              | 9030            | 0.00     |
| INV-7   | 7           | Walk-in                    | 22880           | 0.00      | 190         | Walk-in                              | 2620            | 0.00     |
| INV-8   | 8           | Aminul 4V-Tr               | 11500           | 0.00      | 191         | mahmud - R15 V3                      | 1500            | 1500.00  |
| INV-9   | 9           | Walk-in                    | 22770           | 0.00      | 192         | Walk-in                              | 5540            | 0.00     |
| INV-10  | 10          | Walk-in                    | 9840            | 0.00      | 193         | Walk-in                              | 2880            | 0.00     |
| INV-11  | 11          | Jawad Vai                  | 13700           | 0.00      | 194         | wohi vai - Fzs V2                    | 8500            | 0.00     |
| INV-12  | 12          | Walk-in                    | 8600            | 0.00      | 195         | Walk-in                              | 2450            | 0.00     |
| INV-13  | 13          | Walk-in                    | 5530            | 0.00      | 196         | badal ali                            | 2050            | 2050.00  |
| INV-14  | 14          | Rahul Vai                  | 1450            | 0.00      | 197         | Walk-in                              | 1660            | 0.00     |
| INV-15  | 15          | Kabbo                      | 1700            | 1300.00   | 198         | TR Rahat Vai                         | 400             | 400.00   |
| INV-16  | 16          | Walk-in                    | 1100            | 0.00      | 199         | Mridul vai-Ledth Shop                | 1000            | 1000.00  |
| INV-17  | 17          | Rahamat Hosaain Joy        | 0               | 0.00      | 200         | top care                             | 400             | 0.00     |
| INV-18  | 18          | Nayem Vai                  | 150             | 0.00      | 201         | Quick Shifter                        | 160             | 0.00     |
| INV-19  | 19          | Walk-in                    | 12590           | 0.00      | 202         | jibon technician                     | 350             | 0.00     |
| INV-20  | 20          | Rasel Vai-Savar            | 5900            | 0.00      | 203         | Mridul vai-Ledth Shop                | 100             | 100.00   |
| INV-21  | 21          | Rasel Vai-Savar            | 1200            | 0.00      | 204         | Mridul vai-Ledth Shop                | 300             | 300.00   |
| INV-22  | 22          | Walk-in                    | 2150            | 0.00      | 205         | sabbir pakuria - pulsar              | 2900            | 2750.00  |
| INV-23  | 23          | Fahim Vai                  | 4300            | 0.00      | 206         | jibon technician                     | 460             | 0.00     |
| INV-24  | 24          | Walk-in                    | 900             | 0.00      | 207         | Walk-in                              | 1800            | 0.00     |
| INV-25  | 25          | Walk-in                    | 3710            | 0.00      | 208         | jibon technician                     | 600             | 0.00     |
| INV-26  | 26          | Walk-in                    | 5400            | 0.00      | 209         | Poran vai-FNF Motors                 | 3600            | 0.00     |
| INV-27  | 27          | Masud Vai                  | 2900            | 0.00      | 210         | Poran vai-FNF Motors                 | 2170            | 2170.00  |
| INV-28  | 28          | Walk-in                    | 10300           | 0.00      | 211         | Poran vai-FNF Motors                 | 1000            | 1000.00  |
| INV-29  | 29          | Sazal Vai                  | 1370            | 0.00      | 212         | Poran vai-FNF Motors                 | 1500            | 1500.00  |
| INV-30  | 30          | Walk-in                    | 8270            | -1710.00  | 213         | Poran vai-FNF Motors                 | 1710            | 1710.00  |
| INV-31  | 31          | Walk-in                    | 14830           | -1530.00  | 214         | Motonext (Ohin)                      | 1530            | 1530.00  |
| INV-32  | 32          | Kabbo                      | 1000            | 1000.00   | 215         | Bike Fix BD                          | 150             | 0.00     |
| INV-33  | 33          | Quick Shifter              | 450             | 0.00      | 216         | Chacha-Vatija Motors                 | 750             | 750.00   |
| INV-34  | 34          | Walk-in                    | 18150           | 0.00      | 217         | Chacha-Vatija Motors                 | 2320            | 2320.00  |
| INV-35  | 35          | Walk-in                    | 1320            | 0.00      | 218         | Chacha-Vatija Motors                 | 1750            | 1750.00  |
| INV-36  | 36          | Rabbi                      | 20650           | -2780.00  | 219         | Chacha-Vatija Motors                 | 560             | 560.00   |
| INV-37  | 37          | Walk-in                    | 11190           | -810.00   | 220         | Shemanto                             | 2880            | 2880.00  |
| INV-38  | 38          | Walk-in                    | 1170            | -950.00   | 221         | Rifat Shohid - R15 V2                | 4150            | 1150.00  |
| INV-39  | 39          | Walk-in                    | 3650            | 0.00      | 222         | Walk-in                              | 3000            | 0.00     |
| INV-40  | 40          | Jawad Vai                  | 16810           | 0.00      | 223         | Walk-in                              | 13780           | 0.00     |
| INV-41  | 41          | Walk-in                    | 850             | -3100.00  | 224         | sakib vai - FZ-V2                    | 5800            | 3200.00  |
| INV-42  | 42          | Hamim Vai                  | 24060           | -7800.00  | 225         | Arif-RTR                             | 7800            | 7800.00  |
| INV-43  | 43          | Poran vai-FNF Motors       | 1400            | 0.00      | 226         | Shuvo vai- R15 V3 Blue               | 10400           | 4400.00  |
| INV-44  | 44          | Walk-in                    | 320             | 0.00      | 227         | Walk-in                              | 820             | 0.00     |
| INV-45  | 45          | Farhad Vai Kawla           | 2100            | 0.00      | 228         | Poran Vai-R15 V3                     | 1200            | 0.00     |
| INV-46  | 46          | Walk-in                    | 20490           | -1050.00  | 229         | Poran vai-FNF Motors                 | 1050            | 1050.00  |
| INV-47  | 47          | TR Rahat Vai               | 16430           | -11870.00 | 230         | Dipto                                | 67800           | 32800.00 |
| INV-48  | 48          | Rabbi                      | 8400            | -2300.00  | 231         | rayhan vai - suzuki gixxer - rabbi   | 2300            | 2300.00  |
| INV-49  | 49          | Walk-in                    | 7260            | -1200.00  | 232         | Dipto                                | 1200            | 1200.00  |
| INV-50  | 50          | Aminul 4V-Tr               | 5100            | -500.00   | 233         | sakib vai - FZ-V2                    | 500             | 500.00   |
| INV-52  | 52          | Hamim Vai                  | 3440            | 0.00      | 235         | Walk-in                              | 11140           | 0.00     |
| INV-53  | 53          | Walk-in                    | 14200           | 0.00      | 236         | Walk-in                              | 8450            | 0.00     |
| INV-54  | 54          | Poran vai-FNF Motors       | 1360            | 0.00      | 237         | Walk-in                              | 4680            | 0.00     |
| INV-55  | 55          | Chacha-Vatija Motors       | 1620            | 0.00      | 238         | Dipto                                | 6700            | 6700.00  |
| INV-56  | 56          | Walk-in                    | 5350            | -800.00   | 239         | sakib vai - FZ-V2                    | 800             | 800.00   |
| INV-57  | 57          | Walk-in                    | 700             | 0.00      | 240         | Aminul 4V-Tr                         | 8000            | 0.00     |
| INV-58  | 58          | Hamim Vai                  | 1330            | -1100.00  | 241         | rayhan vai - suzuki gixxer - rabbi   | 1150            | 1150.00  |
| INV-59  | 59          | khan masum vai             | 14750           | 250.00    | 242         | Poran Vai-R15 V3                     | 500             | 500.00   |
| INV-60  | 60          | raj vai                    | 11800           | -2100.00  | 243         | Fahim Vai                            | 2100            | 2100.00  |
| INV-61  | 61          | Walk-in                    | 3200            | 0.00      | 244         | Al- Amin Vai - BH                    | 850             | 0.00     |
| INV-62  | 62          | Walk-in                    | 9800            | 0.00      | 245         | Al- Amin Vai - BH                    | 1850            | 0.00     |
| INV-63  | 63          | Walk-in                    | 20400           | -850.00   | 246         | Poran vai-FNF Motors                 | 850             | 850.00   |
| INV-64  | 64          | Walk-in                    | 8160            | -1100.00  | 247         | Poran vai-FNF Motors                 | 1100            | 1100.00  |
| INV-65  | 65          | Poran vai-FNF Motors       | 5400            | -600.00   | 248         | Poran vai-FNF Motors                 | 600             | 600.00   |
| INV-66  | 66          | Mahabub Sir                | 3280            | -750.00   | 249         | Motonext (Ohin)                      | 750             | 750.00   |
| INV-67  | 67          | Bike Fix BD                | 1200            | -600.00   | 250         | Motonext (Ohin)                      | 600             | 600.00   |
| INV-68  | 68          | Walk-in                    | 16090           | -140.00   | 251         | Motonext (Ohin)                      | 1900            | 1900.00  |
| INV-69  | 69          | mashfiq samit              | 8748            | 0.00      | 252         | Chacha-Vatija Motors                 | 560             | 0.00     |
| INV-70  | 70          | Walk-in                    | 4200            | 0.00      | 253         | Chacha-Vatija Motors                 | 1230            | 1230.00  |
| INV-71  | 71          | Walk-in                    | 3100            | 0.00      | 254         | Chacha-Vatija Motors                 | 630             | 630.00   |
| INV-72  | 72          | TR Rahat Vai               | 4000            | 0.00      | 255         | Walk-in                              | 14350           | 0.00     |
| INV-73  | 73          | Shemanto                   | 300             | 0.00      | 256         | Bike Fix BD                          | 570             | 0.00     |
| INV-74  | 74          | mashfiq samit              | 300             | 0.00      | 257         | fahad vai - TVS 4V - TR Rahat vai    | 1600            | 0.00     |
| INV-75  | 75          | Walk-in                    | 150             | 0.00      | 258         | Al-amin vai- (TR rahat )             | 3100            | 3100.00  |
| INV-76  | 76          | TR Rahat Vai               | 350             | 0.00      | 259         | mahmud - R15 V3                      | 950             | 950.00   |
| INV-77  | 77          | Shop Bike                  | 1320            | 0.00      | 260         | Jilani vai - R15 V3 -                | 7100            | 0.00     |
| INV-80  | 80          | Poran vai-FNF Motors       | 1230            | -9800.00  | 263         | Poran vai-FNF Motors                 | 1400            | 1400.00  |
| INV-81  | 81          | Walk-in                    | 9750            | 0.00      | 264         | Walk-in                              | 180             | 0.00     |
| INV-82  | 82          | Chacha-Vatija Motors       | 80              | 0.00      | 265         | sayed uncle - Discover 125           | 800             | 0.00     |
| INV-83  | 83          | Walk-in                    | 5730            | 0.00      | 266         | Walk-in                              | 270             | 0.00     |
| INV-84  | 84          | Motonext (Ohin)            | 4250            | 0.00      | 267         | Hamim Vai                            | 1450            | 1450.00  |
| INV-85  | 85          | Walk-in                    | 9110            | 0.00      | 270         | Chacha-Vatija Motors                 | 600             | 0.00     |
| INV-86  | 86          | Farhad Vai Kawla           | 9100            | 0.00      | 271         | Chacha-Vatija Motors                 | 400             | 400.00   |
| INV-87  | 87          | Motonext (Ohin)            | 14460           | 0.00      | 272         | Walk-in                              | 3880            | 0.00     |
| INV-88  | 88          | Poran vai-FNF Motors       | 1800            | 0.00      | 273         | sawan vai - Gixxer Monotone          | 2100            | 1200.00  |
| INV-89  | 89          | Walk-in                    | 14440           | 0.00      | 274         | Galib Vai -                          | 4700            | 0.00     |
| INV-90  | 90          | Walk-in                    | 9430            | 0.00      | 275         | Walk-in                              | 300             | 0.00     |
| INV-91  | 92          | Hamim Vai                  | 10450           | 3530.00   | 276         | Walk-in                              | 3150            | 0.00     |
| INV-92  | 93          | Walk-in                    | 17550           | -4430.00  | 277         | Poran vai-FNF Motors                 | 3900            | 3900.00  |
| INV-93  | 94          | Jawad Vai                  | 1200            | -46300.00 | 278         | Rabbi                                | 126300          | 46300.00 |
| INV-94  | 95          | Motonext (Ohin)            | 5040            | -12220.00 | 279         | Rabbi                                | 5050            | 5050.00  |
| INV-95  | 96          | siyam vai(ohin)            | 3190            | 2190.00   | 280         | Walk-in                              | 3300            | 0.00     |
| INV-96  | 97          | Al-amin vai- (TR rahat )   | 7400            | 0.00      | 281         | mahmud - R15 V3                      | 12000           | 8000.00  |
| INV-97  | 98          | Walk-in                    | 21070           | 0.00      | 282         | rayhan vai - suzuki gixxer - rabbi   | 5650            | 150.00   |
| INV-98  | 99          | Poran vai-FNF Motors       | 800             | 0.00      | 283         | mashfiq samit                        | 4720            | 0.00     |
| INV-99  | 100         | Motonext (Ohin)            | 2210            | 0.00      | 284         | Walk-in                              | 770             | 0.00     |
| INV-100 | 101         | sakib vatty-lifan k19      | 2700            | 0.00      | 285         | Motonext (Ohin)                      | 6000            | 6000.00  |
| INV-101 | 102         | Walk-in                    | 2670            | 0.00      | 286         | mashfiq samit                        | 7450            | 6450.00  |
| INV-102 | 103         | Rabbi                      | 530             | 0.00      | 287         | Jawad Vai                            | 200             | 0.00     |
| INV-103 | 104         | Nayem Vai                  | 700             | 0.00      | 288         | Rabbi                                | 250             | 250.00   |
| INV-106 | 107         | Chacha-Vatija Motors       | 235             | 0.00      | 291         | Shemanto                             | 5600            | 4600.00  |
| INV-107 | 108         | Walk-in                    | 6830            | 0.00      | 292         | Walk-in                              | 360             | 0.00     |
| INV-108 | 109         | Rasel Vai-Savar            | 1900            | 0.00      | 293         | Walk-in                              | 4850            | 0.00     |
| INV-109 | 110         | Aminul 4V-Tr               | 1200            | 1200.00   | 294         | sujon Vai - MT 15                    | 1650            | 650.00   |
| INV-110 | 111         | Mridul vai-Ledth Shop      | 300             | -50.00    | 295         | Bike Fix BD                          | 2250            | 50.00    |
| INV-111 | 112         | Sazal Vai                  | 500             | 0.00      | 296         | Walk-in                              | 16080           | 0.00     |
| INV-112 | 113         | Walk-in                    | 2060            | 0.00      | 297         | Galib Vai -                          | 3250            | 0.00     |
| INV-113 | 114         | Walk-in                    | 3700            | 0.00      | 298         | Chacha-Vatija Motors                 | 1220            | 1220.00  |
| INV-114 | 115         | Arif-RTR                   | 7300            | 0.00      | 299         | Chacha-Vatija Motors                 | 1500            | 1500.00  |
| INV-115 | 116         | Poran vai-FNF Motors       | 500             | 0.00      | 300         | Chacha-Vatija Motors                 | 360             | 360.00   |
| INV-116 | 117         | Chacha-Vatija Motors       | 1000            | 0.00      | 301         | Chacha-Vatija Motors                 | 1100            | 1100.00  |
| INV-117 | 118         | Motonext (Ohin)            | 780             | 0.00      | 302         | Chacha-Vatija Motors                 | 800             | 800.00   |
| INV-118 | 119         | Poran Vai-R15 V3           | 2850            | -200.00   | 303         | Bike Fix BD                          | 200             | 200.00   |
| INV-119 | 120         | Walk-in                    | 2600            | -2500.00  | 304         | Sifat Vai - R15 V4                   | 13000           | 2500.00  |
| INV-120 | 121         | Poran vai-FNF Motors       | 2850            | 0.00      | 305         | FZ V3                                | 4250            | 0.00     |
| INV-121 | 122         | Poran Vai-R15 V3           | 5200            | 0.00      | 306         | Walk-in                              | 6300            | 0.00     |
| INV-122 | 123         | Walk-in                    | 2200            | 0.00      | 307         | Walk-in                              | 1500            | 0.00     |
| INV-123 | 124         | Poran vai-FNF Motors       | 1400            | 0.00      | 308         | Walk-in                              | 5320            | 0.00     |
| INV-124 | 125         | Shanto                     | 7600            | 0.00      | 309         | Walk-in                              | 3250            | 0.00     |
| INV-125 | 126         | Walk-in                    | 550             | 0.00      | 310         | mashfiq samit                        | 5400            | 0.00     |
| INV-126 | 127         | Shemanto                   | 4990            | 0.00      | 311         | mashfiq samit                        | 4175            | 4175.00  |
| INV-127 | 128         | Aminul 4V-Tr               | 2150            | 250.00    | 312         | Walk-in                              | 1100            | 0.00     |
| INV-128 | 129         | Walk-in                    | 400             | 0.00      | 313         | Walk-in                              | 8130            | 0.00     |
| INV-129 | 130         | Aminul 4V-Tr               | 2800            | 0.00      | 314         | Walk-in                              | 1500            | 0.00     |
| INV-130 | 131         | Chacha-Vatija Motors       | 1320            | 0.00      | 315         | Walk-in                              | 800             | 0.00     |
| INV-131 | 132         | Hamim Vai                  | 6400            | 6400.00   | 316         | Walk-in                              | 12620           | 0.00     |
| INV-132 | 133         | Walk-in                    | 1180            | 0.00      | 318         | Jawad Vai                            | 1600            | 0.00     |
| INV-133 | 134         | Walk-in                    | 5450            | 0.00      | 319         | fahad vai - TVS 4V - TR Rahat vai    | 3000            | 0.00     |
| INV-134 | 135         | Walk-in                    | 11850           | 0.00      | 320         | Kabbo                                | 6150            | 5150.00  |
| INV-135 | 136         | Chacha-Vatija Motors       | 5440            | -6710.00  | 321         | Shihab Vai - R15 V2                  | 10500           | 8500.00  |
| INV-136 | 137         | laden vai - gixxer         | 9500            | 0.00      | 322         | Walk-in                              | 6500            | 0.00     |
| INV-137 | 138         | taimur vai                 | 20000           | 0.00      | 323         | Walk-in                              | 16860           | 0.00     |
| INV-138 | 142         | Walk-in                    | 9200            | 0.00      | 324         | turan vai - Gixxer                   | 4700            | 0.00     |
| INV-139 | 143         | Rabbi                      | 2500            | 0.00      | 325         | Bike Fix BD                          | 5030            | 0.00     |
| INV-140 | 144         | Hamim Vai                  | 1400            | 1400.00   | 326         | Rabbi                                | 2400            | 2400.00  |
| INV-141 | 145         | Rohan - R15 V3             | 400             | 0.00      | 327         | Imtiaz                               | 3300            | 3200.00  |
| INV-142 | 146         | Poran vai-FNF Motors       | 5160            | 0.00      | 328         | Motonext (Ohin)                      | 540             | 540.00   |
| INV-143 | 147         | Chacha-Vatija Motors       | 60              | 60.00     | 329         | Rabbi                                | 8700            | 8700.00  |
| INV-144 | 148         | Walk-in                    | 2500            | 0.00      | 330         | Walk-in                              | 3880            | 0.00     |
| INV-145 | 149         | Farhad Vai Kawla           | 1550            | 0.00      | 331         | Walk-in                              | 450             | 0.00     |
| INV-146 | 150         | Rabbi                      | 2190            | -17450.00 | 332         | Al-amin vai- (TR rahat )             | 17450           | 17450.00 |
| INV-147 | 151         | sawon Quick Shifter        | 420             | -5380.00  | 333         | Throttle Gear Bangladesh - TVS Rider | 6200            | 6200.00  |
| INV-148 | 152         | TR Rahat Vai               | 1250            | 0.00      | 334         | Walk-in                              | 560             | 0.00     |
| INV-149 | 153         | Nabil-Tongi                | 8000            | 0.00      | 335         | Walk-in                              | 900             | 0.00     |
| INV-150 | 154         | Walk-in                    | 11660           | -12100.00 | 336         | jisan- r15 v2 (shihab vai)           | 12100           | 12100.00 |
| INV-151 | 155         | Chacha-Vatija Motors       | 690             | -3510.00  | 337         | Poran vai-FNF Motors                 | 2100            | 2100.00  |
| INV-152 | 156         | Walk-in                    | 9250            | -1150.00  | 338         | Poran vai-FNF Motors                 | 1150            | 1150.00  |
| INV-153 | 157         | Poran vai-FNF Motors       | 200             | -1500.00  | 339         | Poran vai-FNF Motors                 | 1400            | 1400.00  |
| INV-154 | 158         | Poran vai-FNF Motors       | 250             | -850.00   | 340         | Poran vai-FNF Motors                 | 1500            | 1500.00  |
| INV-155 | 159         | mashfiq samit              | 5750            | 0.00      | 341         | Chacha-Vatija Motors                 | 1780            | 0.00     |
| INV-156 | 160         | mashfiq samit              | 13570           | 1048.00   | 342         | Rabbi                                | 4000            | 4000.00  |
| INV-157 | 161         | Walk-in                    | 6620            | 0.00      | 343         | rayhan vai - suzuki gixxer - rabbi   | 850             | 50.00    |
| INV-158 | 163         | sayed uncle - Discover 125 | 4900            | 0.00      | 344         | Walk-in                              | 100             | 0.00     |
| INV-159 | 164         | Walk-in                    | 8030            | 0.00      | 345         | Motonext (Ohin)                      | 750             | 750.00   |
| INV-161 | 166         | Motonext (Ohin)            | 8850            | 0.00      | 347         | forhad vai                           | 2800            | 0.00     |
| INV-162 | 167         | Shemanto                   | 2950            | 0.00      | 348         | sabbir pakuria - pulsar              | 900             | 400.00   |
| INV-163 | 168         | Walk-in                    | 1000            | 0.00      | 349         | Walk-in                              | 5550            | 0.00     |
| INV-164 | 169         | Bike Fix BD                | 1950            | 0.00      | 350         | Walk-in                              | 8650            | 0.00     |
| INV-165 | 170         | Mridul vai-Ledth Shop      | 1000            | 0.00      | 351         | JOY Vai Market - R15 V3              | 2400            | 0.00     |
| INV-166 | 171         | Walk-in                    | 180             | 0.00      | 352         | Walk-in                              | 100             | 0.00     |
| INV-167 | 172         | Quick Shifter              | 160             | 160.00    | 353         | Walk-in                              | 3700            | 0.00     |
| INV-168 | 173         | Uttara Bike Club           | 170             | -280.00   | 354         | Mahbub Alom - Yamaha - XSR           | 6650            | 450.00   |
| INV-169 | 174         | TR Rahat Vai               | 550             | 0.00      | 355         | Al-amin vai- (TR rahat )             | 7960            | 7960.00  |
| INV-170 | 175         | Motonext (Ohin)            | 910             | 0.00      | 356         | Al-amin vai- (TR rahat )             | 1600            | 1600.00  |
| INV-171 | 176         | Hamim Vai                  | 840             | -6960.00  | 357         | TR Rahat Vai                         | 8800            | 7800.00  |
| INV-172 | 177         | Hamim Vai                  | 3470            | 3470.00   | 358         | Walk-in                              | 2450            | 0.00     |
| INV-173 | 178         | Motonext (Ohin)            | 19230           | 0.00      | 359         | Shemanto                             | 3400            | 400.00   |

---

## 4. Duplicate Invoices with Outstanding Dues (Due > 0)

These are the most critical duplicates - invoices that appear multiple times AND have outstanding due amounts.

### Invoice: `1`

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 180     | 553         | rudro R15m -Nabil Vai | 2025-12-30 | 2000        | 2000.00  | 0.00         | 0.00    | ACTIVE |
| 181     | 0           | Walk-in/Guest         | 2025-12-31 | 10500       | 10500.00 | 0.00         | 0.00    | ACTIVE |
| 182     | 520         | Sazal Vai             | 2025-12-31 | 1150        | 0.00     | 1150.00 \*\* | 0.00    | ACTIVE |
| 183     | 554         | himu vai- pulsar -    | 2025-12-19 | 10000       | 10000.00 | 0.00         | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 85     | 520         | Sazal Vai     | 1150.00    | 0.00        | ACTIVE |

### Invoice: `INV-8`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 8       | 511         | Aminul 4V-Tr    | 2025-11-04 | 11500       | 11500.00 | 0.00         | 0.00    | ACTIVE |
| 191     | 556         | mahmud - R15 V3 | 2026-01-05 | 1500        | 0.00     | 1500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 87     | 556         | mahmud - R15 V3 | 1500.00    | 0.00        | ACTIVE |

### Invoice: `INV-13`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 13      | 0           | Walk-in/Guest | 2025-11-10 | 5530        | 5530.00 | 0.00         | 0.00    | ACTIVE |
| 196     | 503         | badal ali     | 2025-12-13 | 2050        | 0.00    | 2050.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 88     | 503         | badal ali     | 2050.00    | 0.00        | ACTIVE |

### Invoice: `INV-15`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid   | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------ | ------------ | ------- | ------ |
| 15      | 514         | Kabbo         | 2025-11-10 | 1700        | 400.00 | 1300.00 \*\* | 0.00    | ACTIVE |
| 198     | 525         | TR Rahat Vai  | 2026-01-04 | 400         | 0.00   | 400.00 \*\*  | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 2      | 514         | Kabbo         | 1300.00    | 400.00      | ACTIVE |
| 89     | 525         | TR Rahat Vai  | 400.00     | 0.00        | ACTIVE |

### Invoice: `INV-16`

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 16      | 0           | Walk-in/Guest         | 2025-11-10 | 1100        | 1100.00 | 0.00         | 0.00    | ACTIVE |
| 199     | 541         | Mridul vai-Ledth Shop | 2026-01-03 | 1000        | 0.00    | 1000.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name         | Due Amount | Paid Amount | Status  |
| ------ | ----------- | --------------------- | ---------- | ----------- | ------- |
| 90     | 541         | Mridul vai-Ledth Shop | 0.00       | 1000.00     | DELETED |

### Invoice: `INV-20`

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 20      | 517         | Rasel Vai-Savar       | 2025-11-11 | 5900        | 5900.00 | 0.00        | 0.00    | ACTIVE |
| 203     | 541         | Mridul vai-Ledth Shop | 2026-01-05 | 100         | 0.00    | 100.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name         | Due Amount | Paid Amount | Status  |
| ------ | ----------- | --------------------- | ---------- | ----------- | ------- |
| 91     | 541         | Mridul vai-Ledth Shop | 0.00       | 100.00      | DELETED |
| 168    | 517         | Rasel Vai-Savar       | 0.00       | 0.00        | ACTIVE  |

### Invoice: `INV-21`

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 21      | 517         | Rasel Vai-Savar       | 2025-11-11 | 1200        | 1200.00 | 0.00        | 0.00    | ACTIVE |
| 204     | 541         | Mridul vai-Ledth Shop | 2026-01-06 | 300         | 0.00    | 300.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name         | Due Amount | Paid Amount | Status  |
| ------ | ----------- | --------------------- | ---------- | ----------- | ------- |
| 92     | 541         | Mridul vai-Ledth Shop | 0.00       | 300.00      | DELETED |
| 169    | 517         | Rasel Vai-Savar       | 0.00       | 0.00        | ACTIVE  |

### Invoice: `INV-22`

| Sale ID | Customer ID | Customer Name           | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ----------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 22      | 0           | Walk-in/Guest           | 2025-11-12 | 2150        | 2150.00 | 0.00         | 0.00    | ACTIVE |
| 205     | 560         | sabbir pakuria - pulsar | 2026-01-07 | 2900        | 150.00  | 2750.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name           | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ----------------------- | ---------- | ----------- | ------- |
| 93     | 560         | sabbir pakuria - pulsar | 3200.00    | 0.00        | DELETED |
| 193    | 560         | sabbir pakuria - pulsar | 2750.00    | 0.00        | ACTIVE  |

### Invoice: `INV-27`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 27      | 519         | Masud Vai            | 2025-11-14 | 2900        | 2900.00 | 0.00         | 0.00    | ACTIVE |
| 210     | 523         | Poran vai-FNF Motors | 2026-01-04 | 2170        | 0.00    | 2170.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 95     | 523         | Poran vai-FNF Motors | 0.00       | 2170.00     | DELETED |
| 174    | 519         | Masud Vai            | 0.00       | 0.00        | ACTIVE  |

### Invoice: `INV-28`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 28      | 0           | Walk-in/Guest        | 2025-11-14 | 10300       | 10300.00 | 0.00         | 0.00    | ACTIVE |
| 211     | 523         | Poran vai-FNF Motors | 2026-01-05 | 1000        | 0.00     | 1000.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 96     | 523         | Poran vai-FNF Motors | 0.00       | 1000.00     | DELETED |

### Invoice: `INV-29`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 29      | 520         | Sazal Vai            | 2025-11-14 | 1370        | 1370.00 | 0.00         | 0.00    | ACTIVE |
| 212     | 523         | Poran vai-FNF Motors | 2026-01-09 | 1500        | 0.00    | 1500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 97     | 523         | Poran vai-FNF Motors | 0.00       | 1500.00     | DELETED |
| 170    | 520         | Sazal Vai            | 0.00       | 0.00        | DELETED |
| 171    | 520         | Sazal Vai            | 0.00       | 0.00        | ACTIVE  |

### Invoice: `INV-30`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 30      | 0           | Walk-in/Guest        | 2025-11-15 | 8270        | 9980.00 | -1710.00     | 0.00    | ACTIVE |
| 213     | 523         | Poran vai-FNF Motors | 2026-01-10 | 1710        | 0.00    | 1710.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 98     | 523         | Poran vai-FNF Motors | 0.00       | 1710.00     | ACTIVE |

### Invoice: `INV-31`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 31      | 0           | Walk-in/Guest   | 2025-11-16 | 14830       | 16360.00 | -1530.00     | 0.00    | ACTIVE |
| 214     | 536         | Motonext (Ohin) | 2026-01-08 | 1530        | 0.00     | 1530.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 99     | 536         | Motonext (Ohin) | 0.00       | 1530.00     | ACTIVE |

### Invoice: `INV-32`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid   | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------ | ------------ | ------- | ------ |
| 32      | 514         | Kabbo         | 2025-11-16 | 1000        | 0.00   | 1000.00 \*\* | 0.00    | ACTIVE |
| 215     | 530         | Bike Fix BD   | 2026-01-06 | 150         | 150.00 | 0.00         | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 4      | 514         | Kabbo         | 1000.00    | 0.00        | ACTIVE |

### Invoice: `INV-33`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid   | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------ | ----------- | ------- | ------ |
| 33      | 521         | Quick Shifter        | 2025-11-16 | 450         | 450.00 | 0.00        | 0.00    | ACTIVE |
| 216     | 526         | Chacha-Vatija Motors | 2026-01-05 | 750         | 0.00   | 750.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 100    | 526         | Chacha-Vatija Motors | 750.00     | 0.00        | ACTIVE |

### Invoice: `INV-34`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 34      | 0           | Walk-in/Guest        | 2025-11-17 | 18150       | 18150.00 | 0.00         | 0.00    | ACTIVE |
| 217     | 526         | Chacha-Vatija Motors | 2026-01-06 | 2320        | 0.00     | 2320.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 101    | 526         | Chacha-Vatija Motors | 2320.00    | 0.00        | ACTIVE |

### Invoice: `INV-35`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 35      | 0           | Walk-in/Guest        | 2025-11-18 | 1320        | 1320.00 | 0.00         | 0.00    | ACTIVE |
| 218     | 526         | Chacha-Vatija Motors | 2026-01-09 | 1750        | 0.00    | 1750.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 102    | 526         | Chacha-Vatija Motors | 1750.00    | 0.00        | ACTIVE |

### Invoice: `INV-36`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ----------- | ------- | ------ |
| 36      | 522         | Rabbi                | 2025-11-18 | 20650       | 23530.00 | -2780.00    | 0.00    | ACTIVE |
| 219     | 526         | Chacha-Vatija Motors | 2026-01-10 | 560         | 0.00     | 560.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 5      | 522         | Rabbi                | -2880.00   | 2780.00     | ACTIVE |
| 103    | 526         | Chacha-Vatija Motors | 560.00     | 0.00        | ACTIVE |

### Invoice: `INV-37`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 37      | 0           | Walk-in/Guest | 2025-11-19 | 11190       | 12000.00 | -810.00      | 0.00    | ACTIVE |
| 220     | 534         | Shemanto      | 2026-01-06 | 2880        | 0.00     | 2880.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 104    | 534         | Shemanto      | 2070.00    | 810.00      | ACTIVE |

### Invoice: `INV-38`

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 38      | 0           | Walk-in/Guest         | 2025-11-20 | 1170        | 2120.00 | -950.00      | 0.00    | ACTIVE |
| 221     | 561         | Rifat Shohid - R15 V2 | 2026-01-08 | 4150        | 3000.00 | 1150.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name         | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------------- | ---------- | ----------- | ------ |
| 105    | 561         | Rifat Shohid - R15 V2 | 200.00     | 950.00      | ACTIVE |

### Invoice: `INV-41`

| Sale ID | Customer ID | Customer Name     | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ----------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 41      | 0           | Walk-in/Guest     | 2025-11-22 | 850         | 3950.00 | -3100.00     | 0.00    | ACTIVE |
| 224     | 562         | sakib vai - FZ-V2 | 2026-01-10 | 5800        | 2600.00 | 3200.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name     | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ----------------- | ---------- | ----------- | ------- |
| 106    | 562         | sakib vai - FZ-V2 | 1260.00    | 2000.00     | DELETED |
| 135    | 562         | sakib vai - FZ-V2 | 2100.00    | 1100.00     | ACTIVE  |

### Invoice: `INV-42`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 42      | 510         | Hamim Vai     | 2025-11-22 | 24060       | 31860.00 | -7800.00     | 0.00    | ACTIVE |
| 225     | 542         | Arif-RTR      | 2026-01-09 | 7800        | 0.00     | 7800.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ------------- | ---------- | ----------- | ------- |
| 6      | 510         | Hamim Vai     | 0.00       | 8080.00     | DELETED |
| 107    | 542         | Arif-RTR      | 7500.00    | 0.00        | DELETED |
| 109    | 542         | Arif-RTR      | 0.00       | 7800.00     | ACTIVE  |

### Invoice: `INV-43`

| Sale ID | Customer ID | Customer Name          | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ---------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 43      | 523         | Poran vai-FNF Motors   | 2025-11-22 | 1400        | 1400.00 | 0.00         | 0.00    | ACTIVE |
| 226     | 563         | Shuvo vai- R15 V3 Blue | 2026-01-09 | 10400       | 6000.00 | 4400.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name          | Due Amount | Paid Amount | Status |
| ------ | ----------- | ---------------------- | ---------- | ----------- | ------ |
| 108    | 563         | Shuvo vai- R15 V3 Blue | 4400.00    | 0.00        | ACTIVE |

### Invoice: `INV-46`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 46      | 0           | Walk-in/Guest        | 2025-11-23 | 20490       | 21540.00 | -1050.00     | 0.00    | ACTIVE |
| 229     | 523         | Poran vai-FNF Motors | 2026-01-11 | 1050        | 0.00     | 1050.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 110    | 523         | Poran vai-FNF Motors | 0.00       | 1050.00     | ACTIVE |

### Invoice: `INV-47`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount    | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------- | ------- | ------ |
| 47      | 525         | TR Rahat Vai  | 2025-11-23 | 16430       | 28300.00 | -11870.00     | 0.00    | ACTIVE |
| 230     | 564         | Dipto         | 2026-01-12 | 67800       | 35000.00 | 32800.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ------------- | ---------- | ----------- | ------- |
| 7      | 525         | TR Rahat Vai  | 5930.00    | 0.00        | DELETED |
| 111    | 564         | Dipto         | 29800.00   | 0.00        | DELETED |
| 113    | 564         | Dipto         | 32800.00   | 0.00        | DELETED |
| 137    | 564         | Dipto         | 15000.00   | 17800.00    | ACTIVE  |

### Invoice: `INV-48`

| Sale ID | Customer ID | Customer Name                      | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ---------------------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 48      | 522         | Rabbi                              | 2025-11-23 | 8400        | 10700.00 | -2300.00     | 0.00    | ACTIVE |
| 231     | 551         | rayhan vai - suzuki gixxer - rabbi | 2025-12-29 | 2300        | 0.00     | 2300.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name                      | Due Amount | Paid Amount | Status |
| ------ | ----------- | ---------------------------------- | ---------- | ----------- | ------ |
| 112    | 551         | rayhan vai - suzuki gixxer - rabbi | 0.00       | 2300.00     | ACTIVE |

### Invoice: `INV-49`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 49      | 0           | Walk-in/Guest | 2025-11-24 | 7260        | 8460.00 | -1200.00     | 0.00    | ACTIVE |
| 232     | 564         | Dipto         | 2026-01-11 | 1200        | 0.00    | 1200.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 114    | 564         | Dipto         | 0.00       | 1200.00     | ACTIVE |

### Invoice: `INV-50`

| Sale ID | Customer ID | Customer Name     | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | ----------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 50      | 511         | Aminul 4V-Tr      | 2025-11-24 | 5100        | 5600.00 | -500.00     | 0.00    | ACTIVE |
| 233     | 562         | sakib vai - FZ-V2 | 2026-01-11 | 500         | 0.00    | 500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name     | Due Amount | Paid Amount | Status |
| ------ | ----------- | ----------------- | ---------- | ----------- | ------ |
| 115    | 562         | sakib vai - FZ-V2 | 0.00       | 500.00      | ACTIVE |

### Invoice: `INV-55`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 55      | 526         | Chacha-Vatija Motors | 2025-11-26 | 1620        | 1620.00 | 0.00         | 0.00    | ACTIVE |
| 238     | 564         | Dipto                | 2026-01-14 | 6700        | 0.00    | 6700.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ------------- | ---------- | ----------- | ------- |
| 117    | 564         | Dipto         | 7500.00    | 0.00        | DELETED |
| 142    | 564         | Dipto         | 6700.00    | 0.00        | ACTIVE  |

### Invoice: `INV-56`

| Sale ID | Customer ID | Customer Name     | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | ----------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 56      | 0           | Walk-in/Guest     | 2025-11-26 | 5350        | 6150.00 | -800.00     | 0.00    | ACTIVE |
| 239     | 562         | sakib vai - FZ-V2 | 2026-01-14 | 800         | 0.00    | 800.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name     | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ----------------- | ---------- | ----------- | ------- |
| 118    | 562         | sakib vai - FZ-V2 | 900.00     | 0.00        | DELETED |
| 134    | 562         | sakib vai - FZ-V2 | 0.00       | 800.00      | ACTIVE  |

### Invoice: `INV-58`

| Sale ID | Customer ID | Customer Name                      | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ---------------------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 58      | 510         | Hamim Vai                          | 2025-11-26 | 1330        | 2430.00 | -1100.00     | 0.00    | ACTIVE |
| 241     | 551         | rayhan vai - suzuki gixxer - rabbi | 2026-01-14 | 1150        | 0.00    | 1150.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name                      | Due Amount | Paid Amount | Status |
| ------ | ----------- | ---------------------------------- | ---------- | ----------- | ------ |
| 119    | 551         | rayhan vai - suzuki gixxer - rabbi | 50.00      | 1100.00     | ACTIVE |

### Invoice: `INV-59`

| Sale ID | Customer ID | Customer Name    | Order Date | Grand Total | Paid     | Due Amount  | Receive | Status |
| ------- | ----------- | ---------------- | ---------- | ----------- | -------- | ----------- | ------- | ------ |
| 59      | 527         | khan masum vai   | 2025-11-26 | 14750       | 14500.00 | 250.00 \*\* | 0.00    | ACTIVE |
| 242     | 543         | Poran Vai-R15 V3 | 2026-01-14 | 500         | 0.00     | 500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name    | Due Amount | Paid Amount | Status |
| ------ | ----------- | ---------------- | ---------- | ----------- | ------ |
| 9      | 527         | khan masum vai   | 250.00     | 0.00        | ACTIVE |
| 120    | 543         | Poran Vai-R15 V3 | 500.00     | 0.00        | ACTIVE |

### Invoice: `INV-60`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 60      | 528         | raj vai       | 2025-11-26 | 11800       | 13900.00 | -2100.00     | 0.00    | ACTIVE |
| 243     | 518         | Fahim Vai     | 2026-01-14 | 2100        | 0.00     | 2100.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 121    | 518         | Fahim Vai     | 0.00       | 2100.00     | ACTIVE |

### Invoice: `INV-63`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ----------- | ------- | ------ |
| 63      | 0           | Walk-in/Guest        | 2025-11-28 | 20400       | 21250.00 | -850.00     | 0.00    | ACTIVE |
| 246     | 523         | Poran vai-FNF Motors | 2026-01-13 | 850         | 0.00     | 850.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 122    | 523         | Poran vai-FNF Motors | 0.00       | 850.00      | ACTIVE |

### Invoice: `INV-64`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 64      | 0           | Walk-in/Guest        | 2025-11-29 | 8160        | 9260.00 | -1100.00     | 0.00    | ACTIVE |
| 247     | 523         | Poran vai-FNF Motors | 2026-01-14 | 1100        | 0.00    | 1100.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 123    | 523         | Poran vai-FNF Motors | 0.00       | 1100.00     | ACTIVE |

### Invoice: `INV-65`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 65      | 523         | Poran vai-FNF Motors | 2025-11-29 | 5400        | 6000.00 | -600.00     | 0.00    | ACTIVE |
| 248     | 523         | Poran vai-FNF Motors | 2026-01-15 | 600         | 0.00    | 600.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 124    | 523         | Poran vai-FNF Motors | 0.00       | 600.00      | ACTIVE |

### Invoice: `INV-66`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 66      | 531         | Mahabub Sir     | 2025-11-29 | 3280        | 4030.00 | -750.00     | 0.00    | ACTIVE |
| 249     | 536         | Motonext (Ohin) | 2026-01-13 | 750         | 0.00    | 750.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 125    | 536         | Motonext (Ohin) | 0.00       | 750.00      | ACTIVE |

### Invoice: `INV-67`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 67      | 530         | Bike Fix BD     | 2025-11-30 | 1200        | 1800.00 | -600.00     | 0.00    | ACTIVE |
| 250     | 536         | Motonext (Ohin) | 2026-01-14 | 600         | 0.00    | 600.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 126    | 536         | Motonext (Ohin) | 0.00       | 600.00      | ACTIVE |

### Invoice: `INV-68`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 68      | 0           | Walk-in/Guest   | 2025-11-30 | 16090       | 16230.00 | -140.00      | 0.00    | ACTIVE |
| 251     | 536         | Motonext (Ohin) | 2026-01-15 | 1900        | 0.00     | 1900.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 127    | 536         | Motonext (Ohin) | 1760.00    | 140.00      | ACTIVE |

### Invoice: `INV-70`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 70      | 0           | Walk-in/Guest        | 2025-11-30 | 4200        | 4200.00 | 0.00         | 0.00    | ACTIVE |
| 253     | 526         | Chacha-Vatija Motors | 2026-01-13 | 1230        | 0.00    | 1230.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 128    | 526         | Chacha-Vatija Motors | 1250.00    | 0.00        | DELETED |
| 130    | 526         | Chacha-Vatija Motors | 1230.00    | 0.00        | ACTIVE  |

### Invoice: `INV-71`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 71      | 0           | Walk-in/Guest        | 2025-11-30 | 3100        | 3100.00 | 0.00        | 0.00    | ACTIVE |
| 254     | 526         | Chacha-Vatija Motors | 2026-01-14 | 630         | 0.00    | 630.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 129    | 526         | Chacha-Vatija Motors | 630.00     | 0.00        | ACTIVE |

### Invoice: `INV-75`

| Sale ID | Customer ID | Customer Name            | Order Date | Grand Total | Paid   | Due Amount   | Receive | Status |
| ------- | ----------- | ------------------------ | ---------- | ----------- | ------ | ------------ | ------- | ------ |
| 75      | 0           | Walk-in/Guest            | 2025-11-30 | 150         | 150.00 | 0.00         | 0.00    | ACTIVE |
| 258     | 540         | Al-amin vai- (TR rahat ) | 2026-01-15 | 3100        | 0.00   | 3100.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name            | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ------------------------ | ---------- | ----------- | ------- |
| 131    | 540         | Al-amin vai- (TR rahat ) | 3250.00    | 0.00        | DELETED |
| 180    | 540         | Al-amin vai- (TR rahat ) | 3100.00    | 0.00        | ACTIVE  |

### Invoice: `INV-76`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid   | Due Amount  | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | ------ | ----------- | ------- | ------ |
| 76      | 525         | TR Rahat Vai    | 2025-11-30 | 350         | 350.00 | 0.00        | 0.00    | ACTIVE |
| 259     | 556         | mahmud - R15 V3 | 2026-01-15 | 950         | 0.00   | 950.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 14     | 525         | TR Rahat Vai    | 0.00       | 350.00      | ACTIVE |
| 132    | 556         | mahmud - R15 V3 | 950.00     | 0.00        | ACTIVE |

### Invoice: `INV-80`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 80      | 523         | Poran vai-FNF Motors | 2025-12-01 | 1230        | 11030.00 | -9800.00     | 0.00    | ACTIVE |
| 263     | 523         | Poran vai-FNF Motors | 2026-01-16 | 1400        | 0.00     | 1400.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 16     | 523         | Poran vai-FNF Motors | -9800.00   | 11030.00    | ACTIVE |
| 133    | 523         | Poran vai-FNF Motors | 1400.00    | 0.00        | ACTIVE |

### Invoice: `INV-84`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 84      | 536         | Motonext (Ohin) | 2025-12-03 | 4250        | 4250.00 | 0.00         | 0.00    | ACTIVE |
| 267     | 510         | Hamim Vai       | 2026-01-16 | 1450        | 0.00    | 1450.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 18     | 536         | Motonext (Ohin) | 0.00       | 4250.00     | ACTIVE |
| 136    | 510         | Hamim Vai       | 1450.00    | 0.00        | ACTIVE |

### Invoice: `INV-86`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 86      | 524         | Farhad Vai Kawla     | 2025-12-04 | 9100        | 9100.00 | 0.00        | 0.00    | ACTIVE |
| 271     | 526         | Chacha-Vatija Motors | 2026-01-16 | 400         | 0.00    | 400.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 19     | 524         | Farhad Vai Kawla     | 0.00       | 4100.00     | DELETED |
| 138    | 526         | Chacha-Vatija Motors | 400.00     | 0.00        | ACTIVE  |

### Invoice: `INV-88`

| Sale ID | Customer ID | Customer Name               | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | --------------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 88      | 523         | Poran vai-FNF Motors        | 2025-12-04 | 1800        | 1800.00 | 0.00         | 0.00    | ACTIVE |
| 273     | 570         | sawan vai - Gixxer Monotone | 2026-01-17 | 2100        | 900.00  | 1200.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name               | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------------------- | ---------- | ----------- | ------ |
| 21     | 523         | Poran vai-FNF Motors        | 0.00       | 1800.00     | ACTIVE |
| 139    | 570         | sawan vai - Gixxer Monotone | 1200.00    | 0.00        | ACTIVE |

### Invoice: `INV-91`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 92      | 510         | Hamim Vai     | 2025-12-06 | 10450       | 6920.00 | 3530.00 \*\* | 0.00    | ACTIVE |
| 276     | 0           | Walk-in/Guest | 2026-01-18 | 3150        | 3150.00 | 0.00         | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 22     | 510         | Hamim Vai     | 3530.00    | 6920.00     | ACTIVE |

### Invoice: `INV-92`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 93      | 0           | Walk-in/Guest        | 2025-12-07 | 17550       | 21980.00 | -4430.00     | 0.00    | ACTIVE |
| 277     | 523         | Poran vai-FNF Motors | 2026-01-19 | 3900        | 0.00     | 3900.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 140    | 523         | Poran vai-FNF Motors | 3370.00    | 530.00      | DELETED |
| 163    | 523         | Poran vai-FNF Motors | 0.00       | 3900.00     | ACTIVE  |

### Invoice: `INV-93`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount    | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------- | ------- | ------ |
| 94      | 512         | Jawad Vai     | 2025-12-07 | 1200        | 47500.00 | -46300.00     | 0.00    | ACTIVE |
| 278     | 522         | Rabbi         | 2026-01-19 | 126300      | 80000.00 | 46300.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 141    | 522         | Rabbi         | 0.00       | 46300.00    | ACTIVE |

### Invoice: `INV-94`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 95      | 536         | Motonext (Ohin) | 2025-12-07 | 5040        | 17260.00 | -12220.00    | 0.00    | ACTIVE |
| 279     | 522         | Rabbi           | 2026-01-19 | 5050        | 0.00     | 5050.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 23     | 536         | Motonext (Ohin) | -12220.00  | 17260.00    | ACTIVE |
| 143    | 522         | Rabbi           | 5050.00    | 0.00        | ACTIVE |

### Invoice: `INV-95`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 96      | 538         | siyam vai(ohin) | 2025-12-07 | 3190        | 1000.00 | 2190.00 \*\* | 0.00    | ACTIVE |
| 280     | 0           | Walk-in/Guest   | 2026-01-19 | 3300        | 3300.00 | 0.00         | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 24     | 538         | siyam vai(ohin) | 2190.00    | 0.00        | ACTIVE |

### Invoice: `INV-96`

| Sale ID | Customer ID | Customer Name            | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------------------ | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 97      | 540         | Al-amin vai- (TR rahat ) | 2025-12-07 | 7400        | 7400.00 | 0.00         | 0.00    | ACTIVE |
| 281     | 556         | mahmud - R15 V3          | 2026-01-19 | 12000       | 4000.00 | 8000.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name            | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------------------ | ---------- | ----------- | ------ |
| 25     | 540         | Al-amin vai- (TR rahat ) | 0.00       | 5550.00     | ACTIVE |
| 144    | 556         | mahmud - R15 V3          | 8000.00    | 0.00        | ACTIVE |

### Invoice: `INV-97`

| Sale ID | Customer ID | Customer Name                      | Order Date | Grand Total | Paid     | Due Amount  | Receive | Status |
| ------- | ----------- | ---------------------------------- | ---------- | ----------- | -------- | ----------- | ------- | ------ |
| 98      | 0           | Walk-in/Guest                      | 2025-12-08 | 21070       | 21070.00 | 0.00        | 0.00    | ACTIVE |
| 282     | 551         | rayhan vai - suzuki gixxer - rabbi | 2026-01-19 | 5650        | 5500.00  | 150.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name                      | Due Amount | Paid Amount | Status |
| ------ | ----------- | ---------------------------------- | ---------- | ----------- | ------ |
| 145    | 551         | rayhan vai - suzuki gixxer - rabbi | 150.00     | 0.00        | ACTIVE |

### Invoice: `INV-100`

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 101     | 539         | sakib vatty-lifan k19 | 2025-12-08 | 2700        | 2700.00 | 0.00         | 0.00    | ACTIVE |
| 285     | 536         | Motonext (Ohin)       | 2026-01-21 | 6000        | 0.00    | 6000.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name         | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------------- | ---------- | ----------- | ------ |
| 28     | 539         | sakib vatty-lifan k19 | 0.00       | 1200.00     | ACTIVE |
| 147    | 536         | Motonext (Ohin)       | 6000.00    | 0.00        | ACTIVE |

### Invoice: `INV-101`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 102     | 0           | Walk-in/Guest | 2025-12-09 | 2670        | 2670.00 | 0.00         | 0.00    | ACTIVE |
| 286     | 532         | mashfiq samit | 2026-01-20 | 7450        | 1000.00 | 6450.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 148    | 532         | mashfiq samit | 6450.00    | 0.00        | ACTIVE |

### Invoice: `INV-103`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid   | Due Amount  | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------ | ----------- | ------- | ------ |
| 104     | 516         | Nayem Vai     | 2025-12-09 | 700         | 700.00 | 0.00        | 0.00    | ACTIVE |
| 288     | 522         | Rabbi         | 2026-01-21 | 250         | 0.00   | 250.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 149    | 522         | Rabbi         | 250.00     | 0.00        | ACTIVE |

### Invoice: `INV-106`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 107     | 526         | Chacha-Vatija Motors | 2025-12-10 | 235         | 235.00  | 0.00         | 0.00    | ACTIVE |
| 291     | 534         | Shemanto             | 2026-01-21 | 5600        | 1000.00 | 4600.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 30     | 526         | Chacha-Vatija Motors | 0.00       | 235.00      | ACTIVE |
| 152    | 534         | Shemanto             | 4600.00    | 0.00        | ACTIVE |

### Invoice: `INV-109`

| Sale ID | Customer ID | Customer Name     | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ----------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 110     | 511         | Aminul 4V-Tr      | 2025-12-11 | 1200        | 0.00    | 1200.00 \*\* | 0.00    | ACTIVE |
| 294     | 574         | sujon Vai - MT 15 | 2026-01-21 | 1650        | 1000.00 | 650.00 \*\*  | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name     | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ----------------- | ---------- | ----------- | ------- |
| 32     | 511         | Aminul 4V-Tr      | 1200.00    | 0.00        | DELETED |
| 57     | 511         | Aminul 4V-Tr      | 1200.00    | 0.00        | ACTIVE  |
| 153    | 574         | sujon Vai - MT 15 | 650.00     | 0.00        | ACTIVE  |

### Invoice: `INV-110`

| Sale ID | Customer ID | Customer Name         | Order Date | Grand Total | Paid    | Due Amount | Receive | Status |
| ------- | ----------- | --------------------- | ---------- | ----------- | ------- | ---------- | ------- | ------ |
| 111     | 541         | Mridul vai-Ledth Shop | 2025-12-11 | 300         | 350.00  | -50.00     | 0.00    | ACTIVE |
| 295     | 530         | Bike Fix BD           | 2026-01-21 | 2250        | 2200.00 | 50.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name         | Due Amount | Paid Amount | Status  |
| ------ | ----------- | --------------------- | ---------- | ----------- | ------- |
| 33     | 541         | Mridul vai-Ledth Shop | 0.00       | 300.00      | DELETED |
| 158    | 530         | Bike Fix BD           | 0.00       | 50.00       | ACTIVE  |

### Invoice: `INV-113`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 114     | 0           | Walk-in/Guest        | 2025-12-13 | 3700        | 3700.00 | 0.00         | 0.00    | ACTIVE |
| 298     | 526         | Chacha-Vatija Motors | 2026-01-17 | 1220        | 0.00    | 1220.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 154    | 526         | Chacha-Vatija Motors | 1220.00    | 0.00        | ACTIVE |

### Invoice: `INV-114`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 115     | 542         | Arif-RTR             | 2025-12-13 | 7300        | 7300.00 | 0.00         | 0.00    | ACTIVE |
| 299     | 526         | Chacha-Vatija Motors | 2026-01-18 | 1500        | 0.00    | 1500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 155    | 526         | Chacha-Vatija Motors | 1500.00    | 0.00        | ACTIVE |

### Invoice: `INV-115`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid   | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------ | ----------- | ------- | ------ |
| 116     | 523         | Poran vai-FNF Motors | 2025-12-13 | 500         | 500.00 | 0.00        | 0.00    | ACTIVE |
| 300     | 526         | Chacha-Vatija Motors | 2026-01-19 | 360         | 0.00   | 360.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 36     | 523         | Poran vai-FNF Motors | 0.00       | 500.00      | ACTIVE |
| 156    | 526         | Chacha-Vatija Motors | 360.00     | 0.00        | ACTIVE |

### Invoice: `INV-116`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 117     | 526         | Chacha-Vatija Motors | 2025-12-13 | 1000        | 1000.00 | 0.00         | 0.00    | ACTIVE |
| 301     | 526         | Chacha-Vatija Motors | 2026-01-21 | 1100        | 0.00    | 1100.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 37     | 526         | Chacha-Vatija Motors | 0.00       | 1000.00     | ACTIVE |
| 157    | 526         | Chacha-Vatija Motors | 1100.00    | 0.00        | ACTIVE |

### Invoice: `INV-117`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid   | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------ | ----------- | ------- | ------ |
| 118     | 536         | Motonext (Ohin)      | 2025-12-13 | 780         | 780.00 | 0.00        | 0.00    | ACTIVE |
| 302     | 526         | Chacha-Vatija Motors | 2026-01-23 | 800         | 0.00   | 800.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 38     | 536         | Motonext (Ohin)      | 0.00       | 780.00      | ACTIVE |
| 159    | 526         | Chacha-Vatija Motors | 800.00     | 0.00        | ACTIVE |

### Invoice: `INV-118`

| Sale ID | Customer ID | Customer Name    | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | ---------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 119     | 543         | Poran Vai-R15 V3 | 2025-12-13 | 2850        | 3050.00 | -200.00     | 0.00    | ACTIVE |
| 303     | 530         | Bike Fix BD      | 2026-01-23 | 200         | 0.00    | 200.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name    | Due Amount | Paid Amount | Status |
| ------ | ----------- | ---------------- | ---------- | ----------- | ------ |
| 39     | 543         | Poran Vai-R15 V3 | -200.00    | 3050.00     | ACTIVE |
| 160    | 530         | Bike Fix BD      | 200.00     | 0.00        | ACTIVE |

### Invoice: `INV-119`

| Sale ID | Customer ID | Customer Name      | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ------------------ | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 120     | 0           | Walk-in/Guest      | 2025-12-14 | 2600        | 5100.00  | -2500.00     | 0.00    | ACTIVE |
| 304     | 575         | Sifat Vai - R15 V4 | 2026-01-23 | 13000       | 10500.00 | 2500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name      | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------------ | ---------- | ----------- | ------ |
| 161    | 575         | Sifat Vai - R15 V4 | 0.00       | 2500.00     | ACTIVE |

### Invoice: `INV-126`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 127     | 534         | Shemanto      | 2025-12-18 | 4990        | 4990.00 | 0.00         | 0.00    | ACTIVE |
| 311     | 532         | mashfiq samit | 2026-01-24 | 4175        | 0.00    | 4175.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 59     | 534         | Shemanto      | 0.00       | 4990.00     | ACTIVE |
| 162    | 532         | mashfiq samit | 4175.00    | 0.00        | ACTIVE |

### Invoice: `INV-127`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 128     | 511         | Aminul 4V-Tr  | 2025-12-18 | 2150        | 1900.00 | 250.00 \*\* | 0.00    | ACTIVE |
| 312     | 0           | Walk-in/Guest | 2026-01-24 | 1100        | 1100.00 | 0.00        | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 44     | 511         | Aminul 4V-Tr  | 250.00     | 0.00        | ACTIVE |

### Invoice: `INV-131`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 132     | 510         | Hamim Vai     | 2025-12-19 | 6400        | 0.00     | 6400.00 \*\* | 0.00    | ACTIVE |
| 316     | 0           | Walk-in/Guest | 2026-01-26 | 12620       | 12620.00 | 0.00         | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 46     | 510         | Hamim Vai     | 6400.00    | 0.00        | ACTIVE |

### Invoice: `INV-134`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 135     | 0           | Walk-in/Guest | 2025-12-21 | 11850       | 11850.00 | 0.00         | 0.00    | ACTIVE |
| 320     | 514         | Kabbo         | 2026-01-26 | 6150        | 1000.00  | 5150.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 164    | 514         | Kabbo         | 5150.00    | 0.00        | ACTIVE |

### Invoice: `INV-135`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 136     | 526         | Chacha-Vatija Motors | 2025-12-22 | 5440        | 12150.00 | -6710.00     | 0.00    | ACTIVE |
| 321     | 572         | Shihab Vai - R15 V2  | 2026-01-27 | 10500       | 2000.00  | 8500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 47     | 526         | Chacha-Vatija Motors | -6210.00   | 11650.00    | DELETED |
| 165    | 572         | Shihab Vai - R15 V2  | 12200.00   | 0.00        | DELETED |
| 166    | 572         | Shihab Vai - R15 V2  | 8600.00    | 0.00        | DELETED |
| 184    | 572         | Shihab Vai - R15 V2  | 8000.00    | 500.00      | ACTIVE  |

### Invoice: `INV-140`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ---- | ------------ | ------- | ------ |
| 144     | 510         | Hamim Vai     | 2025-12-23 | 1400        | 0.00 | 1400.00 \*\* | 0.00    | ACTIVE |
| 326     | 522         | Rabbi         | 2026-01-24 | 2400        | 0.00 | 2400.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ------------- | ---------- | ----------- | ------- |
| 49     | 510         | Hamim Vai     | 1400.00    | 0.00        | DELETED |
| 172    | 522         | Rabbi         | 10800.00   | 0.00        | DELETED |
| 177    | 522         | Rabbi         | 2400.00    | 0.00        | ACTIVE  |

### Invoice: `INV-141`

| Sale ID | Customer ID | Customer Name  | Order Date | Grand Total | Paid   | Due Amount   | Receive | Status |
| ------- | ----------- | -------------- | ---------- | ----------- | ------ | ------------ | ------- | ------ |
| 145     | 547         | Rohan - R15 V3 | 2025-12-23 | 400         | 400.00 | 0.00         | 0.00    | ACTIVE |
| 327     | 578         | Imtiaz         | 2026-01-29 | 3300        | 100.00 | 3200.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name  | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------- | ---------- | ----------- | ------- |
| 50     | 547         | Rohan - R15 V3 | 420.00     | 0.00        | DELETED |
| 84     | 547         | Rohan - R15 V3 | 0.00       | 400.00      | ACTIVE  |
| 173    | 578         | Imtiaz         | 3200.00    | 0.00        | ACTIVE  |

### Invoice: `INV-142`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 146     | 523         | Poran vai-FNF Motors | 2025-12-23 | 5160        | 5160.00 | 0.00        | 0.00    | ACTIVE |
| 328     | 536         | Motonext (Ohin)      | 2026-01-29 | 540         | 0.00    | 540.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 51     | 523         | Poran vai-FNF Motors | 0.00       | 5160.00     | DELETED |
| 175    | 536         | Motonext (Ohin)      | -5460.00   | 0.00        | DELETED |
| 176    | 536         | Motonext (Ohin)      | 540.00     | 0.00        | ACTIVE  |

### Invoice: `INV-143`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ---- | ------------ | ------- | ------ |
| 147     | 526         | Chacha-Vatija Motors | 2025-12-23 | 60          | 0.00 | 60.00 \*\*   | 0.00    | ACTIVE |
| 329     | 522         | Rabbi                | 2026-01-29 | 8700        | 0.00 | 8700.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 52     | 526         | Chacha-Vatija Motors | 60.00      | 0.00        | ACTIVE |
| 178    | 522         | Rabbi                | 8700.00    | 0.00        | ACTIVE |

### Invoice: `INV-146`

| Sale ID | Customer ID | Customer Name            | Order Date | Grand Total | Paid     | Due Amount    | Receive | Status |
| ------- | ----------- | ------------------------ | ---------- | ----------- | -------- | ------------- | ------- | ------ |
| 150     | 522         | Rabbi                    | 2025-12-24 | 2190        | 19640.00 | -17450.00     | 0.00    | ACTIVE |
| 332     | 540         | Al-amin vai- (TR rahat ) | 2026-01-28 | 17450       | 0.00     | 17450.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name            | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------------------ | ---------- | ----------- | ------ |
| 54     | 522         | Rabbi                    | -17450.00  | 19640.00    | ACTIVE |
| 179    | 540         | Al-amin vai- (TR rahat ) | 17450.00   | 0.00        | ACTIVE |

### Invoice: `INV-147`

| Sale ID | Customer ID | Customer Name                        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------------------------------ | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 151     | 548         | sawon Quick Shifter                  | 2025-12-24 | 420         | 5800.00 | -5380.00     | 0.00    | ACTIVE |
| 333     | 579         | Throttle Gear Bangladesh - TVS Rider | 2026-01-29 | 6200        | 0.00    | 6200.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name                        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ------------------------------------ | ---------- | ----------- | ------- |
| 55     | 548         | sawon Quick Shifter                  | 420.00     | 0.00        | DELETED |
| 181    | 579         | Throttle Gear Bangladesh - TVS Rider | 6560.00    | 0.00        | DELETED |
| 185    | 579         | Throttle Gear Bangladesh - TVS Rider | 400.00     | 5800.00     | ACTIVE  |

### Invoice: `INV-150`

| Sale ID | Customer ID | Customer Name              | Order Date | Grand Total | Paid     | Due Amount    | Receive | Status |
| ------- | ----------- | -------------------------- | ---------- | ----------- | -------- | ------------- | ------- | ------ |
| 154     | 0           | Walk-in/Guest              | 2025-12-25 | 11660       | 23760.00 | -12100.00     | 0.00    | ACTIVE |
| 336     | 580         | jisan- r15 v2 (shihab vai) | 2026-01-30 | 12100       | 0.00     | 12100.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name              | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------------- | ---------- | ----------- | ------- |
| 182    | 580         | jisan- r15 v2 (shihab vai) | 12200.00   | 0.00        | DELETED |
| 183    | 580         | jisan- r15 v2 (shihab vai) | 0.00       | 12100.00    | ACTIVE  |

### Invoice: `INV-151`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 155     | 526         | Chacha-Vatija Motors | 2025-12-24 | 690         | 4200.00 | -3510.00     | 0.00    | ACTIVE |
| 337     | 523         | Poran vai-FNF Motors | 2026-01-21 | 2100        | 0.00    | 2100.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 60     | 526         | Chacha-Vatija Motors | -3510.00   | 4200.00     | ACTIVE |
| 186    | 523         | Poran vai-FNF Motors | 2100.00    | 0.00        | ACTIVE |

### Invoice: `INV-152`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 156     | 0           | Walk-in/Guest        | 2025-12-26 | 9250        | 10400.00 | -1150.00     | 0.00    | ACTIVE |
| 338     | 523         | Poran vai-FNF Motors | 2026-01-22 | 1150        | 0.00     | 1150.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------ |
| 187    | 523         | Poran vai-FNF Motors | 0.00       | 1150.00     | ACTIVE |

### Invoice: `INV-153`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 157     | 523         | Poran vai-FNF Motors | 2025-12-29 | 200         | 1700.00 | -1500.00     | 0.00    | ACTIVE |
| 339     | 523         | Poran vai-FNF Motors | 2026-01-25 | 1400        | 0.00    | 1400.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 61     | 523         | Poran vai-FNF Motors | 450.00     | 0.00        | DELETED |
| 62     | 523         | Poran vai-FNF Motors | 200.00     | 0.00        | DELETED |
| 65     | 523         | Poran vai-FNF Motors | 200.00     | 0.00        | DELETED |
| 66     | 523         | Poran vai-FNF Motors | -1500.00   | 1700.00     | ACTIVE  |
| 188    | 523         | Poran vai-FNF Motors | 1400.00    | 0.00        | ACTIVE  |

### Invoice: `INV-154`

| Sale ID | Customer ID | Customer Name        | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | -------------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 158     | 523         | Poran vai-FNF Motors | 2025-12-28 | 250         | 1100.00 | -850.00      | 0.00    | ACTIVE |
| 340     | 523         | Poran vai-FNF Motors | 2026-01-31 | 1500        | 0.00    | 1500.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name        | Due Amount | Paid Amount | Status  |
| ------ | ----------- | -------------------- | ---------- | ----------- | ------- |
| 63     | 523         | Poran vai-FNF Motors | 250.00     | 0.00        | DELETED |
| 64     | 523         | Poran vai-FNF Motors | 250.00     | 0.00        | DELETED |
| 78     | 523         | Poran vai-FNF Motors | -850.00    | 1100.00     | ACTIVE  |
| 189    | 523         | Poran vai-FNF Motors | 1500.00    | 0.00        | ACTIVE  |

### Invoice: `INV-156`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid     | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | -------- | ------------ | ------- | ------ |
| 160     | 532         | mashfiq samit | 2025-12-29 | 13570       | 12522.00 | 1048.00 \*\* | 0.00    | ACTIVE |
| 342     | 522         | Rabbi         | 2026-01-31 | 4000        | 0.00     | 4000.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 68     | 532         | mashfiq samit | 1048.00    | 12522.00    | ACTIVE |
| 190    | 522         | Rabbi         | 4000.00    | 0.00        | ACTIVE |

### Invoice: `INV-157`

| Sale ID | Customer ID | Customer Name                      | Order Date | Grand Total | Paid    | Due Amount | Receive | Status |
| ------- | ----------- | ---------------------------------- | ---------- | ----------- | ------- | ---------- | ------- | ------ |
| 161     | 0           | Walk-in/Guest                      | 2025-12-28 | 6620        | 6620.00 | 0.00       | 0.00    | ACTIVE |
| 343     | 551         | rayhan vai - suzuki gixxer - rabbi | 2026-01-31 | 850         | 800.00  | 50.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name                      | Due Amount | Paid Amount | Status |
| ------ | ----------- | ---------------------------------- | ---------- | ----------- | ------ |
| 191    | 551         | rayhan vai - suzuki gixxer - rabbi | 50.00      | 0.00        | ACTIVE |

### Invoice: `INV-159`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 164     | 0           | Walk-in/Guest   | 2025-12-29 | 8030        | 8030.00 | 0.00        | 0.00    | ACTIVE |
| 345     | 536         | Motonext (Ohin) | 2026-02-02 | 750         | 0.00    | 750.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 192    | 536         | Motonext (Ohin) | 750.00     | 0.00        | ACTIVE |

### Invoice: `INV-162`

| Sale ID | Customer ID | Customer Name           | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | ----------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 167     | 534         | Shemanto                | 2025-12-31 | 2950        | 2950.00 | 0.00        | 0.00    | ACTIVE |
| 348     | 560         | sabbir pakuria - pulsar | 2026-02-02 | 900         | 500.00  | 400.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name           | Due Amount | Paid Amount | Status |
| ------ | ----------- | ----------------------- | ---------- | ----------- | ------ |
| 73     | 534         | Shemanto                | 0.00       | 950.00      | ACTIVE |
| 194    | 560         | sabbir pakuria - pulsar | 400.00     | 0.00        | ACTIVE |

### Invoice: `INV-167`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 172     | 521         | Quick Shifter | 2025-12-29 | 160         | 0.00    | 160.00 \*\* | 0.00    | ACTIVE |
| 353     | 0           | Walk-in/Guest | 2026-02-03 | 3700        | 3700.00 | 0.00        | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 75     | 521         | Quick Shifter | 160.00     | 0.00        | ACTIVE |

### Invoice: `INV-168`

| Sale ID | Customer ID | Customer Name              | Order Date | Grand Total | Paid    | Due Amount  | Receive | Status |
| ------- | ----------- | -------------------------- | ---------- | ----------- | ------- | ----------- | ------- | ------ |
| 173     | 552         | Uttara Bike Club           | 2025-12-30 | 170         | 450.00  | -280.00     | 0.00    | ACTIVE |
| 354     | 584         | Mahbub Alom - Yamaha - XSR | 2026-02-03 | 6650        | 6200.00 | 450.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name              | Due Amount | Paid Amount | Status |
| ------ | ----------- | -------------------------- | ---------- | ----------- | ------ |
| 76     | 552         | Uttara Bike Club           | -280.00    | 450.00      | ACTIVE |
| 195    | 584         | Mahbub Alom - Yamaha - XSR | 450.00     | 0.00        | ACTIVE |

### Invoice: `INV-169`

| Sale ID | Customer ID | Customer Name            | Order Date | Grand Total | Paid   | Due Amount   | Receive | Status |
| ------- | ----------- | ------------------------ | ---------- | ----------- | ------ | ------------ | ------- | ------ |
| 174     | 525         | TR Rahat Vai             | 2025-12-31 | 550         | 550.00 | 0.00         | 0.00    | ACTIVE |
| 355     | 540         | Al-amin vai- (TR rahat ) | 2026-02-04 | 7960        | 0.00   | 7960.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name            | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------------------ | ---------- | ----------- | ------ |
| 77     | 525         | TR Rahat Vai             | 0.00       | 550.00      | ACTIVE |
| 196    | 540         | Al-amin vai- (TR rahat ) | 7960.00    | 0.00        | ACTIVE |

### Invoice: `INV-170`

| Sale ID | Customer ID | Customer Name            | Order Date | Grand Total | Paid   | Due Amount   | Receive | Status |
| ------- | ----------- | ------------------------ | ---------- | ----------- | ------ | ------------ | ------- | ------ |
| 175     | 536         | Motonext (Ohin)          | 2025-12-31 | 910         | 910.00 | 0.00         | 0.00    | ACTIVE |
| 356     | 540         | Al-amin vai- (TR rahat ) | 2026-02-04 | 1600        | 0.00   | 1600.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name            | Due Amount | Paid Amount | Status  |
| ------ | ----------- | ------------------------ | ---------- | ----------- | ------- |
| 79     | 536         | Motonext (Ohin)          | 910.00     | 0.00        | DELETED |
| 82     | 536         | Motonext (Ohin)          | 0.00       | 910.00      | DELETED |
| 197    | 540         | Al-amin vai- (TR rahat ) | 3700.00    | 0.00        | DELETED |
| 199    | 540         | Al-amin vai- (TR rahat ) | 1600.00    | 0.00        | ACTIVE  |

### Invoice: `INV-171`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 176     | 510         | Hamim Vai     | 2025-12-30 | 840         | 7800.00 | -6960.00     | 0.00    | ACTIVE |
| 357     | 525         | TR Rahat Vai  | 2026-02-04 | 8800        | 1000.00 | 7800.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 80     | 510         | Hamim Vai     | -6960.00   | 7800.00     | ACTIVE |
| 198    | 525         | TR Rahat Vai  | 7800.00    | 0.00        | ACTIVE |

### Invoice: `INV-172`

| Sale ID | Customer ID | Customer Name | Order Date | Grand Total | Paid    | Due Amount   | Receive | Status |
| ------- | ----------- | ------------- | ---------- | ----------- | ------- | ------------ | ------- | ------ |
| 177     | 510         | Hamim Vai     | 2025-12-31 | 3470        | 0.00    | 3470.00 \*\* | 0.00    | ACTIVE |
| 358     | 0           | Walk-in/Guest | 2026-02-04 | 2450        | 2450.00 | 0.00         | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
| ------ | ----------- | ------------- | ---------- | ----------- | ------ |
| 81     | 510         | Hamim Vai     | 3470.00    | 0.00        | ACTIVE |

### Invoice: `INV-173`

| Sale ID | Customer ID | Customer Name   | Order Date | Grand Total | Paid     | Due Amount  | Receive | Status |
| ------- | ----------- | --------------- | ---------- | ----------- | -------- | ----------- | ------- | ------ |
| 178     | 536         | Motonext (Ohin) | 2025-12-03 | 19230       | 19230.00 | 0.00        | 0.00    | ACTIVE |
| 359     | 534         | Shemanto        | 2026-02-04 | 3400        | 3000.00  | 400.00 \*\* | 0.00    | ACTIVE |

**Customer Dues (Due Receive):**

| Due ID | Customer ID | Customer Name   | Due Amount | Paid Amount | Status |
| ------ | ----------- | --------------- | ---------- | ----------- | ------ |
| 83     | 536         | Motonext (Ohin) | 0.00       | 19230.00    | ACTIVE |
| 200    | 534         | Shemanto        | 400.00     | 0.00        | ACTIVE |

---

## 5. Complete Duplicate Invoice List

| #   | Invoice | Sale ID | Customer ID | Customer Name         | Sales Date | Amount |
| --- | ------- | ------- | ----------- | --------------------- | ---------- | ------ |
| 1   | 1       | 180     | 553         | rudro R15m -Nabil Vai | 2025-12-30 | 2000   |
| 2   | 1       | 181     | 0           | Walk-in/Guest         | 2025-12-31 | 10500  |
| 3   | 1       | 182     | 520         | Sazal Vai             | 2025-12-31 | 1150   |
| 4   | 1       | 183     | 554         | himu vai- pulsar -    | 2025-12-19 | 10000  |

| 19 | INV-8 | 8 | 511 | Aminul 4V-Tr | 2025-11-04 | 11500 |
| 20 | INV-8 | 191 | 556 | mahmud - R15 V3 | 2026-01-05 | 1500 |
| 21 | INV-9 | 9 | 0 | Walk-in/Guest | 2025-11-05 | 22770 |
| 22 | INV-9 | 192 | 0 | Walk-in/Guest | 2026-01-06 | 5540 |
| 23 | INV-10 | 10 | 0 | Walk-in/Guest | 2025-11-06 | 9840 |
| 24 | INV-10 | 193 | 0 | Walk-in/Guest | 2026-01-07 | 2880 |
| 25 | INV-11 | 11 | 512 | Jawad Vai | 2025-11-06 | 13700 |
| 26 | INV-11 | 194 | 557 | wohi vai - Fzs V2 | 2026-01-07 | 8500 |
| 27 | INV-12 | 12 | 0 | Walk-in/Guest | 2025-11-06 | 8600 |
| 28 | INV-12 | 195 | 0 | Walk-in/Guest | 2026-01-07 | 2450 |
| 29 | INV-13 | 13 | 0 | Walk-in/Guest | 2025-11-10 | 5530 |
| 30 | INV-13 | 196 | 503 | badal ali | 2025-12-13 | 2050 |
| 31 | INV-14 | 14 | 513 | Rahul Vai | 2025-11-10 | 1450 |
| 32 | INV-14 | 197 | 0 | Walk-in/Guest | 2026-01-08 | 1660 |
| 33 | INV-15 | 15 | 514 | Kabbo | 2025-11-10 | 1700 |
| 34 | INV-15 | 198 | 525 | TR Rahat Vai | 2026-01-04 | 400 |
| 35 | INV-16 | 16 | 0 | Walk-in/Guest | 2025-11-10 | 1100 |
| 36 | INV-16 | 199 | 541 | Mridul vai-Ledth Shop | 2026-01-03 | 1000 |
| 37 | INV-17 | 17 | 515 | Rahamat Hosaain Joy | 2025-11-10 | 0 |
| 38 | INV-17 | 200 | 558 | top care | 2026-01-05 | 400 |
| 39 | INV-18 | 18 | 516 | Nayem Vai | 2025-11-10 | 150 |
| 40 | INV-18 | 201 | 521 | Quick Shifter | 2026-01-05 | 160 |
| 41 | INV-19 | 19 | 0 | Walk-in/Guest | 2025-11-11 | 12590 |
| 42 | INV-19 | 202 | 559 | jibon technician | 2026-01-05 | 350 |
| 43 | INV-20 | 20 | 517 | Rasel Vai-Savar | 2025-11-11 | 5900 |
| 44 | INV-20 | 203 | 541 | Mridul vai-Ledth Shop | 2026-01-05 | 100 |
| 45 | INV-21 | 21 | 517 | Rasel Vai-Savar | 2025-11-11 | 1200 |
| 46 | INV-21 | 204 | 541 | Mridul vai-Ledth Shop | 2026-01-06 | 300 |
| 47 | INV-22 | 22 | 0 | Walk-in/Guest | 2025-11-12 | 2150 |
| 48 | INV-22 | 205 | 560 | sabbir pakuria - pulsar | 2026-01-07 | 2900 |
| 49 | INV-23 | 23 | 518 | Fahim Vai | 2025-11-12 | 4300 |
| 50 | INV-23 | 206 | 559 | jibon technician | 2026-01-08 | 460 |
| 51 | INV-24 | 24 | 0 | Walk-in/Guest | 2025-11-13 | 900 |
| 52 | INV-24 | 207 | 0 | Walk-in/Guest | 2025-12-31 | 1800 |
| 53 | INV-25 | 25 | 0 | Walk-in/Guest | 2025-11-13 | 3710 |
| 54 | INV-25 | 208 | 559 | jibon technician | 2025-12-09 | 600 |
| 55 | INV-26 | 26 | 0 | Walk-in/Guest | 2025-11-13 | 5400 |
| 56 | INV-26 | 209 | 523 | Poran vai-FNF Motors | 2026-01-03 | 3600 |
| 57 | INV-27 | 27 | 519 | Masud Vai | 2025-11-14 | 2900 |
| 58 | INV-27 | 210 | 523 | Poran vai-FNF Motors | 2026-01-04 | 2170 |
| 59 | INV-28 | 28 | 0 | Walk-in/Guest | 2025-11-14 | 10300 |
| 60 | INV-28 | 211 | 523 | Poran vai-FNF Motors | 2026-01-05 | 1000 |
| 61 | INV-29 | 29 | 520 | Sazal Vai | 2025-11-14 | 1370 |
| 62 | INV-29 | 212 | 523 | Poran vai-FNF Motors | 2026-01-09 | 1500 |
| 63 | INV-30 | 30 | 0 | Walk-in/Guest | 2025-11-15 | 8270 |
| 64 | INV-30 | 213 | 523 | Poran vai-FNF Motors | 2026-01-10 | 1710 |
| 65 | INV-31 | 31 | 0 | Walk-in/Guest | 2025-11-16 | 14830 |
| 66 | INV-31 | 214 | 536 | Motonext (Ohin) | 2026-01-08 | 1530 |
| 67 | INV-32 | 32 | 514 | Kabbo | 2025-11-16 | 1000 |
| 68 | INV-32 | 215 | 530 | Bike Fix BD | 2026-01-06 | 150 |
| 69 | INV-33 | 33 | 521 | Quick Shifter | 2025-11-16 | 450 |
| 70 | INV-33 | 216 | 526 | Chacha-Vatija Motors | 2026-01-05 | 750 |
| 71 | INV-34 | 34 | 0 | Walk-in/Guest | 2025-11-17 | 18150 |
| 72 | INV-34 | 217 | 526 | Chacha-Vatija Motors | 2026-01-06 | 2320 |
| 73 | INV-35 | 35 | 0 | Walk-in/Guest | 2025-11-18 | 1320 |
| 74 | INV-35 | 218 | 526 | Chacha-Vatija Motors | 2026-01-09 | 1750 |
| 75 | INV-36 | 36 | 522 | Rabbi | 2025-11-18 | 20650 |
| 76 | INV-36 | 219 | 526 | Chacha-Vatija Motors | 2026-01-10 | 560 |
| 77 | INV-37 | 37 | 0 | Walk-in/Guest | 2025-11-19 | 11190 |
| 78 | INV-37 | 220 | 534 | Shemanto | 2026-01-06 | 2880 |
| 79 | INV-38 | 38 | 0 | Walk-in/Guest | 2025-11-20 | 1170 |
| 80 | INV-38 | 221 | 561 | Rifat Shohid - R15 V2 | 2026-01-08 | 4150 |
| 81 | INV-39 | 39 | 0 | Walk-in/Guest | 2025-11-21 | 3650 |
| 82 | INV-39 | 222 | 0 | Walk-in/Guest | 2026-01-09 | 3000 |
| 83 | INV-40 | 40 | 512 | Jawad Vai | 2025-11-22 | 16810 |
| 84 | INV-40 | 223 | 0 | Walk-in/Guest | 2026-01-10 | 13780 |
| 85 | INV-41 | 41 | 0 | Walk-in/Guest | 2025-11-22 | 850 |
| 86 | INV-41 | 224 | 562 | sakib vai - FZ-V2 | 2026-01-10 | 5800 |
| 87 | INV-42 | 42 | 510 | Hamim Vai | 2025-11-22 | 24060 |
| 88 | INV-42 | 225 | 542 | Arif-RTR | 2026-01-09 | 7800 |
| 89 | INV-43 | 43 | 523 | Poran vai-FNF Motors | 2025-11-22 | 1400 |
| 90 | INV-43 | 226 | 563 | Shuvo vai- R15 V3 Blue | 2026-01-09 | 10400 |
| 91 | INV-44 | 44 | 0 | Walk-in/Guest | 2025-11-22 | 320 |
| 92 | INV-44 | 227 | 0 | Walk-in/Guest | 2026-01-11 | 820 |
| 93 | INV-45 | 45 | 524 | Farhad Vai Kawla | 2025-11-22 | 2100 |
| 94 | INV-45 | 228 | 543 | Poran Vai-R15 V3 | 2026-01-11 | 1200 |
| 95 | INV-46 | 46 | 0 | Walk-in/Guest | 2025-11-23 | 20490 |
| 96 | INV-46 | 229 | 523 | Poran vai-FNF Motors | 2026-01-11 | 1050 |
| 97 | INV-47 | 47 | 525 | TR Rahat Vai | 2025-11-23 | 16430 |
| 98 | INV-47 | 230 | 564 | Dipto | 2026-01-12 | 67800 |
| 99 | INV-48 | 48 | 522 | Rabbi | 2025-11-23 | 8400 |
| 100 | INV-48 | 231 | 551 | rayhan vai - suzuki gixxer - rabbi | 2025-12-29 | 2300 |
| 101 | INV-49 | 49 | 0 | Walk-in/Guest | 2025-11-24 | 7260 |
| 102 | INV-49 | 232 | 564 | Dipto | 2026-01-11 | 1200 |
| 103 | INV-50 | 50 | 511 | Aminul 4V-Tr | 2025-11-24 | 5100 |
| 104 | INV-50 | 233 | 562 | sakib vai - FZ-V2 | 2026-01-11 | 500 |
| 105 | INV-52 | 52 | 510 | Hamim Vai | 2025-11-25 | 3440 |
| 106 | INV-52 | 235 | 0 | Walk-in/Guest | 2026-01-12 | 11140 |
| 107 | INV-53 | 53 | 0 | Walk-in/Guest | 2025-11-25 | 14200 |
| 108 | INV-53 | 236 | 0 | Walk-in/Guest | 2026-01-13 | 8450 |
| 109 | INV-54 | 54 | 523 | Poran vai-FNF Motors | 2025-11-26 | 1360 |
| 110 | INV-54 | 237 | 0 | Walk-in/Guest | 2026-01-14 | 4680 |
| 111 | INV-55 | 55 | 526 | Chacha-Vatija Motors | 2025-11-26 | 1620 |
| 112 | INV-55 | 238 | 564 | Dipto | 2026-01-14 | 6700 |
| 113 | INV-56 | 56 | 0 | Walk-in/Guest | 2025-11-26 | 5350 |
| 114 | INV-56 | 239 | 562 | sakib vai - FZ-V2 | 2026-01-14 | 800 |
| 115 | INV-57 | 57 | 0 | Walk-in/Guest | 2025-11-26 | 700 |
| 116 | INV-57 | 240 | 511 | Aminul 4V-Tr | 2026-01-14 | 8000 |
| 117 | INV-58 | 58 | 510 | Hamim Vai | 2025-11-26 | 1330 |
| 118 | INV-58 | 241 | 551 | rayhan vai - suzuki gixxer - rabbi | 2026-01-14 | 1150 |
| 119 | INV-59 | 59 | 527 | khan masum vai | 2025-11-26 | 14750 |
| 120 | INV-59 | 242 | 543 | Poran Vai-R15 V3 | 2026-01-14 | 500 |
| 121 | INV-60 | 60 | 528 | raj vai | 2025-11-26 | 11800 |
| 122 | INV-60 | 243 | 518 | Fahim Vai | 2026-01-14 | 2100 |
| 123 | INV-61 | 61 | 0 | Walk-in/Guest | 2025-11-26 | 3200 |
| 124 | INV-61 | 244 | 565 | Al- Amin Vai - BH | 2026-01-11 | 850 |
| 125 | INV-62 | 62 | 0 | Walk-in/Guest | 2025-11-27 | 9800 |
| 126 | INV-62 | 245 | 565 | Al- Amin Vai - BH | 2026-01-15 | 1850 |
| 127 | INV-63 | 63 | 0 | Walk-in/Guest | 2025-11-28 | 20400 |
| 128 | INV-63 | 246 | 523 | Poran vai-FNF Motors | 2026-01-13 | 850 |
| 129 | INV-64 | 64 | 0 | Walk-in/Guest | 2025-11-29 | 8160 |
| 130 | INV-64 | 247 | 523 | Poran vai-FNF Motors | 2026-01-14 | 1100 |
| 131 | INV-65 | 65 | 523 | Poran vai-FNF Motors | 2025-11-29 | 5400 |
| 132 | INV-65 | 248 | 523 | Poran vai-FNF Motors | 2026-01-15 | 600 |
| 133 | INV-66 | 66 | 531 | Mahabub Sir | 2025-11-29 | 3280 |
| 134 | INV-66 | 249 | 536 | Motonext (Ohin) | 2026-01-13 | 750 |
| 135 | INV-67 | 67 | 530 | Bike Fix BD | 2025-11-30 | 1200 |
| 136 | INV-67 | 250 | 536 | Motonext (Ohin) | 2026-01-14 | 600 |
| 137 | INV-68 | 68 | 0 | Walk-in/Guest | 2025-11-30 | 16090 |
| 138 | INV-68 | 251 | 536 | Motonext (Ohin) | 2026-01-15 | 1900 |
| 139 | INV-69 | 69 | 532 | mashfiq samit | 2025-11-30 | 8748 |
| 140 | INV-69 | 252 | 526 | Chacha-Vatija Motors | 2026-01-12 | 560 |
| 141 | INV-70 | 70 | 0 | Walk-in/Guest | 2025-11-30 | 4200 |
| 142 | INV-70 | 253 | 526 | Chacha-Vatija Motors | 2026-01-13 | 1230 |
| 143 | INV-71 | 71 | 0 | Walk-in/Guest | 2025-11-30 | 3100 |
| 144 | INV-71 | 254 | 526 | Chacha-Vatija Motors | 2026-01-14 | 630 |
| 145 | INV-72 | 72 | 525 | TR Rahat Vai | 2025-11-30 | 4000 |
| 146 | INV-72 | 255 | 0 | Walk-in/Guest | 2026-01-15 | 14350 |
| 147 | INV-73 | 73 | 534 | Shemanto | 2025-11-30 | 300 |
| 148 | INV-73 | 256 | 530 | Bike Fix BD | 2026-01-15 | 570 |
| 149 | INV-74 | 74 | 532 | mashfiq samit | 2025-11-30 | 300 |
| 150 | INV-74 | 257 | 566 | fahad vai - TVS 4V - TR Rahat vai | 2026-01-14 | 1600 |
| 151 | INV-75 | 75 | 0 | Walk-in/Guest | 2025-11-30 | 150 |
| 152 | INV-75 | 258 | 540 | Al-amin vai- (TR rahat ) | 2026-01-15 | 3100 |
| 153 | INV-76 | 76 | 525 | TR Rahat Vai | 2025-11-30 | 350 |
| 154 | INV-76 | 259 | 556 | mahmud - R15 V3 | 2026-01-15 | 950 |
| 155 | INV-77 | 77 | 535 | Shop Bike | 2025-11-30 | 1320 |
| 156 | INV-77 | 260 | 567 | Jilani vai - R15 V3 - | 2026-01-15 | 7100 |
| 157 | INV-80 | 80 | 523 | Poran vai-FNF Motors | 2025-12-01 | 1230 |
| 158 | INV-80 | 263 | 523 | Poran vai-FNF Motors | 2026-01-16 | 1400 |
| 159 | INV-81 | 81 | 0 | Walk-in/Guest | 2025-12-02 | 9750 |
| 160 | INV-81 | 264 | 0 | Walk-in/Guest | 2026-01-16 | 180 |
| 161 | INV-82 | 82 | 526 | Chacha-Vatija Motors | 2025-12-02 | 80 |
| 162 | INV-82 | 265 | 550 | sayed uncle - Discover 125 | 2026-01-16 | 800 |
| 163 | INV-83 | 83 | 0 | Walk-in/Guest | 2025-12-03 | 5730 |
| 164 | INV-83 | 266 | 0 | Walk-in/Guest | 2026-01-16 | 270 |
| 165 | INV-84 | 84 | 536 | Motonext (Ohin) | 2025-12-03 | 4250 |
| 166 | INV-84 | 267 | 510 | Hamim Vai | 2026-01-16 | 1450 |
| 167 | INV-85 | 85 | 0 | Walk-in/Guest | 2025-12-04 | 9110 |
| 168 | INV-85 | 270 | 526 | Chacha-Vatija Motors | 2026-01-17 | 600 |
| 169 | INV-86 | 86 | 524 | Farhad Vai Kawla | 2025-12-04 | 9100 |
| 170 | INV-86 | 271 | 526 | Chacha-Vatija Motors | 2026-01-16 | 400 |
| 171 | INV-87 | 87 | 536 | Motonext (Ohin) | 2025-12-04 | 14460 |
| 172 | INV-87 | 272 | 0 | Walk-in/Guest | 2026-01-17 | 3880 |
| 173 | INV-88 | 88 | 523 | Poran vai-FNF Motors | 2025-12-04 | 1800 |
| 174 | INV-88 | 273 | 570 | sawan vai - Gixxer Monotone | 2026-01-17 | 2100 |
| 175 | INV-89 | 89 | 0 | Walk-in/Guest | 2025-12-05 | 14440 |
| 176 | INV-89 | 274 | 571 | Galib Vai - | 2026-01-17 | 4700 |
| 177 | INV-90 | 90 | 0 | Walk-in/Guest | 2025-12-06 | 9430 |
| 178 | INV-90 | 275 | 0 | Walk-in/Guest | 2026-01-17 | 300 |
| 179 | INV-91 | 92 | 510 | Hamim Vai | 2025-12-06 | 10450 |
| 180 | INV-91 | 276 | 0 | Walk-in/Guest | 2026-01-18 | 3150 |
| 181 | INV-92 | 93 | 0 | Walk-in/Guest | 2025-12-07 | 17550 |
| 182 | INV-92 | 277 | 523 | Poran vai-FNF Motors | 2026-01-19 | 3900 |
| 183 | INV-93 | 94 | 512 | Jawad Vai | 2025-12-07 | 1200 |
| 184 | INV-93 | 278 | 522 | Rabbi | 2026-01-19 | 126300 |
| 185 | INV-94 | 95 | 536 | Motonext (Ohin) | 2025-12-07 | 5040 |
| 186 | INV-94 | 279 | 522 | Rabbi | 2026-01-19 | 5050 |
| 187 | INV-95 | 96 | 538 | siyam vai(ohin) | 2025-12-07 | 3190 |
| 188 | INV-95 | 280 | 0 | Walk-in/Guest | 2026-01-19 | 3300 |
| 189 | INV-96 | 97 | 540 | Al-amin vai- (TR rahat ) | 2025-12-07 | 7400 |
| 190 | INV-96 | 281 | 556 | mahmud - R15 V3 | 2026-01-19 | 12000 |
| 191 | INV-97 | 98 | 0 | Walk-in/Guest | 2025-12-08 | 21070 |
| 192 | INV-97 | 282 | 551 | rayhan vai - suzuki gixxer - rabbi | 2026-01-19 | 5650 |
| 193 | INV-98 | 99 | 523 | Poran vai-FNF Motors | 2025-12-08 | 800 |
| 194 | INV-98 | 283 | 532 | mashfiq samit | 2026-01-19 | 4720 |
| 195 | INV-99 | 100 | 536 | Motonext (Ohin) | 2025-12-08 | 2210 |
| 196 | INV-99 | 284 | 0 | Walk-in/Guest | 2026-01-20 | 770 |
| 197 | INV-100 | 101 | 539 | sakib vatty-lifan k19 | 2025-12-08 | 2700 |
| 198 | INV-100 | 285 | 536 | Motonext (Ohin) | 2026-01-21 | 6000 |
| 199 | INV-101 | 102 | 0 | Walk-in/Guest | 2025-12-09 | 2670 |
| 200 | INV-101 | 286 | 532 | mashfiq samit | 2026-01-20 | 7450 |
| 201 | INV-102 | 103 | 522 | Rabbi | 2025-12-09 | 530 |
| 202 | INV-102 | 287 | 512 | Jawad Vai | 2026-01-20 | 200 |
| 203 | INV-103 | 104 | 516 | Nayem Vai | 2025-12-09 | 700 |
| 204 | INV-103 | 288 | 522 | Rabbi | 2026-01-21 | 250 |
| 205 | INV-106 | 107 | 526 | Chacha-Vatija Motors | 2025-12-10 | 235 |
| 206 | INV-106 | 291 | 534 | Shemanto | 2026-01-21 | 5600 |
| 207 | INV-107 | 108 | 0 | Walk-in/Guest | 2025-12-11 | 6830 |
| 208 | INV-107 | 292 | 0 | Walk-in/Guest | 2026-01-21 | 360 |
| 209 | INV-108 | 109 | 517 | Rasel Vai-Savar | 2025-12-11 | 1900 |
| 210 | INV-108 | 293 | 0 | Walk-in/Guest | 2026-01-21 | 4850 |
| 211 | INV-109 | 110 | 511 | Aminul 4V-Tr | 2025-12-11 | 1200 |
| 212 | INV-109 | 294 | 574 | sujon Vai - MT 15 | 2026-01-21 | 1650 |
| 213 | INV-110 | 111 | 541 | Mridul vai-Ledth Shop | 2025-12-11 | 300 |
| 214 | INV-110 | 295 | 530 | Bike Fix BD | 2026-01-21 | 2250 |
| 215 | INV-111 | 112 | 520 | Sazal Vai | 2025-12-11 | 500 |
| 216 | INV-111 | 296 | 0 | Walk-in/Guest | 2026-01-22 | 16080 |
| 217 | INV-112 | 113 | 0 | Walk-in/Guest | 2025-12-12 | 2060 |
| 218 | INV-112 | 297 | 571 | Galib Vai - | 2026-01-22 | 3250 |
| 219 | INV-113 | 114 | 0 | Walk-in/Guest | 2025-12-13 | 3700 |
| 220 | INV-113 | 298 | 526 | Chacha-Vatija Motors | 2026-01-17 | 1220 |
| 221 | INV-114 | 115 | 542 | Arif-RTR | 2025-12-13 | 7300 |
| 222 | INV-114 | 299 | 526 | Chacha-Vatija Motors | 2026-01-18 | 1500 |
| 223 | INV-115 | 116 | 523 | Poran vai-FNF Motors | 2025-12-13 | 500 |
| 224 | INV-115 | 300 | 526 | Chacha-Vatija Motors | 2026-01-19 | 360 |
| 225 | INV-116 | 117 | 526 | Chacha-Vatija Motors | 2025-12-13 | 1000 |
| 226 | INV-116 | 301 | 526 | Chacha-Vatija Motors | 2026-01-21 | 1100 |
| 227 | INV-117 | 118 | 536 | Motonext (Ohin) | 2025-12-13 | 780 |
| 228 | INV-117 | 302 | 526 | Chacha-Vatija Motors | 2026-01-23 | 800 |
| 229 | INV-118 | 119 | 543 | Poran Vai-R15 V3 | 2025-12-13 | 2850 |
| 230 | INV-118 | 303 | 530 | Bike Fix BD | 2026-01-23 | 200 |
| 231 | INV-119 | 120 | 0 | Walk-in/Guest | 2025-12-14 | 2600 |
| 232 | INV-119 | 304 | 575 | Sifat Vai - R15 V4 | 2026-01-23 | 13000 |
| 233 | INV-120 | 121 | 523 | Poran vai-FNF Motors | 2025-12-14 | 2850 |
| 234 | INV-120 | 305 | 576 | FZ V3 | 2026-01-23 | 4250 |
| 235 | INV-121 | 122 | 543 | Poran Vai-R15 V3 | 2025-12-15 | 5200 |
| 236 | INV-121 | 306 | 0 | Walk-in/Guest | 2026-01-23 | 6300 |
| 237 | INV-122 | 123 | 0 | Walk-in/Guest | 2025-12-16 | 2200 |
| 238 | INV-122 | 307 | 0 | Walk-in/Guest | 2026-01-23 | 1500 |
| 239 | INV-123 | 124 | 523 | Poran vai-FNF Motors | 2025-12-16 | 1400 |
| 240 | INV-123 | 308 | 0 | Walk-in/Guest | 2026-01-24 | 5320 |
| 241 | INV-124 | 125 | 544 | Shanto | 2025-12-17 | 7600 |
| 242 | INV-124 | 309 | 0 | Walk-in/Guest | 2026-01-24 | 3250 |
| 243 | INV-125 | 126 | 0 | Walk-in/Guest | 2025-12-18 | 550 |
| 244 | INV-125 | 310 | 532 | mashfiq samit | 2026-01-24 | 5400 |
| 245 | INV-126 | 127 | 534 | Shemanto | 2025-12-18 | 4990 |
| 246 | INV-126 | 311 | 532 | mashfiq samit | 2026-01-24 | 4175 |
| 247 | INV-127 | 128 | 511 | Aminul 4V-Tr | 2025-12-18 | 2150 |
| 248 | INV-127 | 312 | 0 | Walk-in/Guest | 2026-01-24 | 1100 |
| 249 | INV-128 | 129 | 0 | Walk-in/Guest | 2025-12-19 | 400 |
| 250 | INV-128 | 313 | 0 | Walk-in/Guest | 2026-01-25 | 8130 |
| 251 | INV-129 | 130 | 511 | Aminul 4V-Tr | 2025-12-19 | 2800 |
| 252 | INV-129 | 314 | 0 | Walk-in/Guest | 2026-01-25 | 1500 |
| 253 | INV-130 | 131 | 526 | Chacha-Vatija Motors | 2025-12-19 | 1320 |
| 254 | INV-130 | 315 | 0 | Walk-in/Guest | 2026-01-25 | 800 |
| 255 | INV-131 | 132 | 510 | Hamim Vai | 2025-12-19 | 6400 |
| 256 | INV-131 | 316 | 0 | Walk-in/Guest | 2026-01-26 | 12620 |
| 257 | INV-132 | 133 | 0 | Walk-in/Guest | 2025-12-20 | 1180 |
| 258 | INV-132 | 318 | 512 | Jawad Vai | 2026-01-26 | 1600 |
| 259 | INV-133 | 134 | 0 | Walk-in/Guest | 2025-12-22 | 5450 |
| 260 | INV-133 | 319 | 566 | fahad vai - TVS 4V - TR Rahat vai | 2026-01-26 | 3000 |
| 261 | INV-134 | 135 | 0 | Walk-in/Guest | 2025-12-21 | 11850 |
| 262 | INV-134 | 320 | 514 | Kabbo | 2026-01-26 | 6150 |
| 263 | INV-135 | 136 | 526 | Chacha-Vatija Motors | 2025-12-22 | 5440 |
| 264 | INV-135 | 321 | 572 | Shihab Vai - R15 V2 | 2026-01-27 | 10500 |
| 265 | INV-136 | 137 | 545 | laden vai - gixxer | 2025-12-22 | 9500 |
| 266 | INV-136 | 322 | 0 | Walk-in/Guest | 2026-01-27 | 6500 |
| 267 | INV-137 | 138 | 546 | taimur vai | 2025-12-22 | 20000 |
| 268 | INV-137 | 323 | 0 | Walk-in/Guest | 2026-01-28 | 16860 |
| 269 | INV-138 | 142 | 0 | Walk-in/Guest | 2025-12-23 | 9200 |
| 270 | INV-138 | 324 | 577 | turan vai - Gixxer | 2026-01-28 | 4700 |
| 271 | INV-139 | 143 | 522 | Rabbi | 2025-12-23 | 2500 |
| 272 | INV-139 | 325 | 530 | Bike Fix BD | 2026-01-28 | 5030 |
| 273 | INV-140 | 144 | 510 | Hamim Vai | 2025-12-23 | 1400 |
| 274 | INV-140 | 326 | 522 | Rabbi | 2026-01-24 | 2400 |
| 275 | INV-141 | 145 | 547 | Rohan - R15 V3 | 2025-12-23 | 400 |
| 276 | INV-141 | 327 | 578 | Imtiaz | 2026-01-29 | 3300 |
| 277 | INV-142 | 146 | 523 | Poran vai-FNF Motors | 2025-12-23 | 5160 |
| 278 | INV-142 | 328 | 536 | Motonext (Ohin) | 2026-01-29 | 540 |
| 279 | INV-143 | 147 | 526 | Chacha-Vatija Motors | 2025-12-23 | 60 |
| 280 | INV-143 | 329 | 522 | Rabbi | 2026-01-29 | 8700 |
| 281 | INV-144 | 148 | 0 | Walk-in/Guest | 2025-12-24 | 2500 |
| 282 | INV-144 | 330 | 0 | Walk-in/Guest | 2026-01-29 | 3880 |
| 283 | INV-145 | 149 | 524 | Farhad Vai Kawla | 2025-12-24 | 1550 |
| 284 | INV-145 | 331 | 0 | Walk-in/Guest | 2026-01-29 | 450 |
| 285 | INV-146 | 150 | 522 | Rabbi | 2025-12-24 | 2190 |
| 286 | INV-146 | 332 | 540 | Al-amin vai- (TR rahat ) | 2026-01-28 | 17450 |
| 287 | INV-147 | 151 | 548 | sawon Quick Shifter | 2025-12-24 | 420 |
| 288 | INV-147 | 333 | 579 | Throttle Gear Bangladesh - TVS Rider | 2026-01-29 | 6200 |
| 289 | INV-148 | 152 | 525 | TR Rahat Vai | 2025-12-24 | 1250 |
| 290 | INV-148 | 334 | 0 | Walk-in/Guest | 2026-01-30 | 560 |
| 291 | INV-149 | 153 | 549 | Nabil-Tongi | 2025-12-13 | 8000 |
| 292 | INV-149 | 335 | 0 | Walk-in/Guest | 2026-01-30 | 900 |
| 293 | INV-150 | 154 | 0 | Walk-in/Guest | 2025-12-25 | 11660 |
| 294 | INV-150 | 336 | 580 | jisan- r15 v2 (shihab vai) | 2026-01-30 | 12100 |
| 295 | INV-151 | 155 | 526 | Chacha-Vatija Motors | 2025-12-24 | 690 |
| 296 | INV-151 | 337 | 523 | Poran vai-FNF Motors | 2026-01-21 | 2100 |
| 297 | INV-152 | 156 | 0 | Walk-in/Guest | 2025-12-26 | 9250 |
| 298 | INV-152 | 338 | 523 | Poran vai-FNF Motors | 2026-01-22 | 1150 |
| 299 | INV-153 | 157 | 523 | Poran vai-FNF Motors | 2025-12-29 | 200 |
| 300 | INV-153 | 339 | 523 | Poran vai-FNF Motors | 2026-01-25 | 1400 |
| 301 | INV-154 | 158 | 523 | Poran vai-FNF Motors | 2025-12-28 | 250 |
| 302 | INV-154 | 340 | 523 | Poran vai-FNF Motors | 2026-01-31 | 1500 |
| 303 | INV-155 | 159 | 532 | mashfiq samit | 2025-12-29 | 5750 |
| 304 | INV-155 | 341 | 526 | Chacha-Vatija Motors | 2026-01-31 | 1780 |
| 305 | INV-156 | 160 | 532 | mashfiq samit | 2025-12-29 | 13570 |
| 306 | INV-156 | 342 | 522 | Rabbi | 2026-01-31 | 4000 |
| 307 | INV-157 | 161 | 0 | Walk-in/Guest | 2025-12-28 | 6620 |
| 308 | INV-157 | 343 | 551 | rayhan vai - suzuki gixxer - rabbi | 2026-01-31 | 850 |
| 309 | INV-158 | 163 | 550 | sayed uncle - Discover 125 | 2025-12-28 | 4900 |
| 310 | INV-158 | 344 | 0 | Walk-in/Guest | 2026-01-31 | 100 |
| 311 | INV-159 | 164 | 0 | Walk-in/Guest | 2025-12-29 | 8030 |
| 312 | INV-159 | 345 | 536 | Motonext (Ohin) | 2026-02-02 | 750 |
| 313 | INV-161 | 166 | 536 | Motonext (Ohin) | 2025-12-29 | 8850 |
| 314 | INV-161 | 347 | 582 | forhad vai | 2026-02-02 | 2800 |
| 315 | INV-162 | 167 | 534 | Shemanto | 2025-12-31 | 2950 |
| 316 | INV-162 | 348 | 560 | sabbir pakuria - pulsar | 2026-02-02 | 900 |
| 317 | INV-163 | 168 | 0 | Walk-in/Guest | 2025-12-31 | 1000 |
| 318 | INV-163 | 349 | 0 | Walk-in/Guest | 2026-02-01 | 5550 |
| 319 | INV-164 | 169 | 530 | Bike Fix BD | 2025-12-25 | 1950 |
| 320 | INV-164 | 350 | 0 | Walk-in/Guest | 2026-02-02 | 8650 |
| 321 | INV-165 | 170 | 541 | Mridul vai-Ledth Shop | 2025-12-26 | 1000 |
| 322 | INV-165 | 351 | 583 | JOY Vai Market - R15 V3 | 2026-02-02 | 2400 |
| 323 | INV-166 | 171 | 0 | Walk-in/Guest | 2025-12-28 | 180 |
| 324 | INV-166 | 352 | 0 | Walk-in/Guest | 2026-02-02 | 100 |
| 325 | INV-167 | 172 | 521 | Quick Shifter | 2025-12-29 | 160 |
| 326 | INV-167 | 353 | 0 | Walk-in/Guest | 2026-02-03 | 3700 |
| 327 | INV-168 | 173 | 552 | Uttara Bike Club | 2025-12-30 | 170 |
| 328 | INV-168 | 354 | 584 | Mahbub Alom - Yamaha - XSR | 2026-02-03 | 6650 |
| 329 | INV-169 | 174 | 525 | TR Rahat Vai | 2025-12-31 | 550 |
| 330 | INV-169 | 355 | 540 | Al-amin vai- (TR rahat ) | 2026-02-04 | 7960 |
| 331 | INV-170 | 175 | 536 | Motonext (Ohin) | 2025-12-31 | 910 |
| 332 | INV-170 | 356 | 540 | Al-amin vai- (TR rahat ) | 2026-02-04 | 1600 |
| 333 | INV-171 | 176 | 510 | Hamim Vai | 2025-12-30 | 840 |
| 334 | INV-171 | 357 | 525 | TR Rahat Vai | 2026-02-04 | 8800 |
| 335 | INV-172 | 177 | 510 | Hamim Vai | 2025-12-31 | 3470 |
| 336 | INV-172 | 358 | 0 | Walk-in/Guest | 2026-02-04 | 2450 |
| 337 | INV-173 | 178 | 536 | Motonext (Ohin) | 2025-12-03 | 19230 |
| 338 | INV-173 | 359 | 534 | Shemanto | 2026-02-04 | 3400 |

**Total duplicate entries:** 338

---

## 6. Duplicate Invoices in Due Receive List (customer_dues)

These are `customer_dues` entries whose invoice number also appears as a duplicate in the sales table.

**Total entries:** 190

| # | Due ID | Invoice | Customer ID | Customer Name | Due Amount | Paid Amount | Status |
|---|--------|---------|-------------|---------------|------------|-------------|--------|
| 1 | 85 | 1 | 520 | Sazal Vai | 1150.00 | 0.00 | ACTIVE |
| 2 | 86 | INV-4 | 520 | Sazal Vai | -1150.00 | 0.00 | DELETED |
| 3 | 87 | INV-8 | 556 | mahmud - R15 V3 | 1500.00 | 0.00 | ACTIVE |
| 4 | 88 | INV-13 | 503 | badal ali | 2050.00 | 0.00 | ACTIVE |
| 5 | 1 | INV-14 | 513 | Rahul Vai | 0.00 | 1450.00 | ACTIVE |
| 6 | 2 | INV-15 | 514 | Kabbo | 1300.00 | 400.00 | ACTIVE |
| 7 | 89 | INV-15 | 525 | TR Rahat Vai | 400.00 | 0.00 | ACTIVE |
| 8 | 90 | INV-16 | 541 | Mridul vai-Ledth Shop | 0.00 | 1000.00 | DELETED |
| 9 | 91 | INV-20 | 541 | Mridul vai-Ledth Shop | 0.00 | 100.00 | DELETED |
| 10 | 168 | INV-20 | 517 | Rasel Vai-Savar | 0.00 | 0.00 | ACTIVE |
| 11 | 92 | INV-21 | 541 | Mridul vai-Ledth Shop | 0.00 | 300.00 | DELETED |
| 12 | 169 | INV-21 | 517 | Rasel Vai-Savar | 0.00 | 0.00 | ACTIVE |
| 13 | 93 | INV-22 | 560 | sabbir pakuria - pulsar | 3200.00 | 0.00 | DELETED |
| 14 | 193 | INV-22 | 560 | sabbir pakuria - pulsar | 2750.00 | 0.00 | ACTIVE |
| 15 | 3 | INV-23 | 518 | Fahim Vai | 0.00 | 1000.00 | ACTIVE |
| 16 | 94 | INV-25 | 559 | jibon technician | 600.00 | 0.00 | DELETED |
| 17 | 95 | INV-27 | 523 | Poran vai-FNF Motors | 0.00 | 2170.00 | DELETED |
| 18 | 174 | INV-27 | 519 | Masud Vai | 0.00 | 0.00 | ACTIVE |
| 19 | 96 | INV-28 | 523 | Poran vai-FNF Motors | 0.00 | 1000.00 | DELETED |
| 20 | 97 | INV-29 | 523 | Poran vai-FNF Motors | 0.00 | 1500.00 | DELETED |
| 21 | 170 | INV-29 | 520 | Sazal Vai | 0.00 | 0.00 | DELETED |
| 22 | 171 | INV-29 | 520 | Sazal Vai | 0.00 | 0.00 | ACTIVE |
| 23 | 98 | INV-30 | 523 | Poran vai-FNF Motors | 0.00 | 1710.00 | ACTIVE |
| 24 | 99 | INV-31 | 536 | Motonext (Ohin) | 0.00 | 1530.00 | ACTIVE |
| 25 | 4 | INV-32 | 514 | Kabbo | 1000.00 | 0.00 | ACTIVE |
| 26 | 100 | INV-33 | 526 | Chacha-Vatija Motors | 750.00 | 0.00 | ACTIVE |
| 27 | 101 | INV-34 | 526 | Chacha-Vatija Motors | 2320.00 | 0.00 | ACTIVE |
| 28 | 102 | INV-35 | 526 | Chacha-Vatija Motors | 1750.00 | 0.00 | ACTIVE |
| 29 | 5 | INV-36 | 522 | Rabbi | -2880.00 | 2780.00 | ACTIVE |
| 30 | 103 | INV-36 | 526 | Chacha-Vatija Motors | 560.00 | 0.00 | ACTIVE |
| 31 | 104 | INV-37 | 534 | Shemanto | 2070.00 | 810.00 | ACTIVE |
| 32 | 105 | INV-38 | 561 | Rifat Shohid - R15 V2 | 200.00 | 950.00 | ACTIVE |
| 33 | 106 | INV-41 | 562 | sakib vai - FZ-V2 | 1260.00 | 2000.00 | DELETED |
| 34 | 135 | INV-41 | 562 | sakib vai - FZ-V2 | 2100.00 | 1100.00 | ACTIVE |
| 35 | 6 | INV-42 | 510 | Hamim Vai | 0.00 | 8080.00 | DELETED |
| 36 | 107 | INV-42 | 542 | Arif-RTR | 7500.00 | 0.00 | DELETED |
| 37 | 109 | INV-42 | 542 | Arif-RTR | 0.00 | 7800.00 | ACTIVE |
| 38 | 108 | INV-43 | 563 | Shuvo vai- R15 V3 Blue | 4400.00 | 0.00 | ACTIVE |
| 39 | 110 | INV-46 | 523 | Poran vai-FNF Motors | 0.00 | 1050.00 | ACTIVE |
| 40 | 7 | INV-47 | 525 | TR Rahat Vai | 5930.00 | 0.00 | DELETED |
| 41 | 111 | INV-47 | 564 | Dipto | 29800.00 | 0.00 | DELETED |
| 42 | 113 | INV-47 | 564 | Dipto | 32800.00 | 0.00 | DELETED |
| 43 | 137 | INV-47 | 564 | Dipto | 15000.00 | 17800.00 | ACTIVE |
| 44 | 112 | INV-48 | 551 | rayhan vai - suzuki gixxer - rabbi | 0.00 | 2300.00 | ACTIVE |
| 45 | 114 | INV-49 | 564 | Dipto | 0.00 | 1200.00 | ACTIVE |
| 46 | 115 | INV-50 | 562 | sakib vai - FZ-V2 | 0.00 | 500.00 | ACTIVE |
| 47 | 117 | INV-55 | 564 | Dipto | 7500.00 | 0.00 | DELETED |
| 48 | 142 | INV-55 | 564 | Dipto | 6700.00 | 0.00 | ACTIVE |
| 49 | 118 | INV-56 | 562 | sakib vai - FZ-V2 | 900.00 | 0.00 | DELETED |
| 50 | 134 | INV-56 | 562 | sakib vai - FZ-V2 | 0.00 | 800.00 | ACTIVE |
| 51 | 119 | INV-58 | 551 | rayhan vai - suzuki gixxer - rabbi | 50.00 | 1100.00 | ACTIVE |
| 52 | 9 | INV-59 | 527 | khan masum vai | 250.00 | 0.00 | ACTIVE |
| 53 | 120 | INV-59 | 543 | Poran Vai-R15 V3 | 500.00 | 0.00 | ACTIVE |
| 54 | 121 | INV-60 | 518 | Fahim Vai | 0.00 | 2100.00 | ACTIVE |
| 55 | 122 | INV-63 | 523 | Poran vai-FNF Motors | 0.00 | 850.00 | ACTIVE |
| 56 | 123 | INV-64 | 523 | Poran vai-FNF Motors | 0.00 | 1100.00 | ACTIVE |
| 57 | 124 | INV-65 | 523 | Poran vai-FNF Motors | 0.00 | 600.00 | ACTIVE |
| 58 | 125 | INV-66 | 536 | Motonext (Ohin) | 0.00 | 750.00 | ACTIVE |
| 59 | 126 | INV-67 | 536 | Motonext (Ohin) | 0.00 | 600.00 | ACTIVE |
| 60 | 127 | INV-68 | 536 | Motonext (Ohin) | 1760.00 | 140.00 | ACTIVE |
| 61 | 10 | INV-69 | 532 | mashfiq samit | 0.00 | 3748.00 | ACTIVE |
| 62 | 128 | INV-70 | 526 | Chacha-Vatija Motors | 1250.00 | 0.00 | DELETED |
| 63 | 130 | INV-70 | 526 | Chacha-Vatija Motors | 1230.00 | 0.00 | ACTIVE |
| 64 | 129 | INV-71 | 526 | Chacha-Vatija Motors | 630.00 | 0.00 | ACTIVE |
| 65 | 11 | INV-72 | 525 | TR Rahat Vai | 0.00 | 4000.00 | ACTIVE |
| 66 | 12 | INV-73 | 534 | Shemanto | 0.00 | 300.00 | ACTIVE |
| 67 | 13 | INV-74 | 532 | mashfiq samit | 0.00 | 300.00 | ACTIVE |
| 68 | 131 | INV-75 | 540 | Al-amin vai- (TR rahat ) | 3250.00 | 0.00 | DELETED |
| 69 | 180 | INV-75 | 540 | Al-amin vai- (TR rahat ) | 3100.00 | 0.00 | ACTIVE |
| 70 | 14 | INV-76 | 525 | TR Rahat Vai | 0.00 | 350.00 | ACTIVE |
| 71 | 132 | INV-76 | 556 | mahmud - R15 V3 | 950.00 | 0.00 | ACTIVE |
| 72 | 16 | INV-80 | 523 | Poran vai-FNF Motors | -9800.00 | 11030.00 | ACTIVE |
| 73 | 133 | INV-80 | 523 | Poran vai-FNF Motors | 1400.00 | 0.00 | ACTIVE |
| 74 | 17 | INV-82 | 526 | Chacha-Vatija Motors | 0.00 | 80.00 | ACTIVE |
| 75 | 18 | INV-84 | 536 | Motonext (Ohin) | 0.00 | 4250.00 | ACTIVE |
| 76 | 136 | INV-84 | 510 | Hamim Vai | 1450.00 | 0.00 | ACTIVE |
| 77 | 19 | INV-86 | 524 | Farhad Vai Kawla | 0.00 | 4100.00 | DELETED |
| 78 | 138 | INV-86 | 526 | Chacha-Vatija Motors | 400.00 | 0.00 | ACTIVE |
| 79 | 20 | INV-87 | 536 | Motonext (Ohin) | 0.00 | 2460.00 | DELETED |
| 80 | 21 | INV-88 | 523 | Poran vai-FNF Motors | 0.00 | 1800.00 | ACTIVE |
| 81 | 139 | INV-88 | 570 | sawan vai - Gixxer Monotone | 1200.00 | 0.00 | ACTIVE |
| 82 | 22 | INV-91 | 510 | Hamim Vai | 3530.00 | 6920.00 | ACTIVE |
| 83 | 140 | INV-92 | 523 | Poran vai-FNF Motors | 3370.00 | 530.00 | DELETED |
| 84 | 163 | INV-92 | 523 | Poran vai-FNF Motors | 0.00 | 3900.00 | ACTIVE |
| 85 | 141 | INV-93 | 522 | Rabbi | 0.00 | 46300.00 | ACTIVE |
| 86 | 23 | INV-94 | 536 | Motonext (Ohin) | -12220.00 | 17260.00 | ACTIVE |
| 87 | 143 | INV-94 | 522 | Rabbi | 5050.00 | 0.00 | ACTIVE |
| 88 | 24 | INV-95 | 538 | siyam vai(ohin) | 2190.00 | 0.00 | ACTIVE |
| 89 | 25 | INV-96 | 540 | Al-amin vai- (TR rahat ) | 0.00 | 5550.00 | ACTIVE |
| 90 | 144 | INV-96 | 556 | mahmud - R15 V3 | 8000.00 | 0.00 | ACTIVE |
| 91 | 145 | INV-97 | 551 | rayhan vai - suzuki gixxer - rabbi | 150.00 | 0.00 | ACTIVE |
| 92 | 26 | INV-98 | 523 | Poran vai-FNF Motors | 0.00 | 800.00 | DELETED |
| 93 | 146 | INV-98 | 532 | mashfiq samit | 0.00 | 0.00 | ACTIVE |
| 94 | 27 | INV-99 | 536 | Motonext (Ohin) | 0.00 | 2210.00 | ACTIVE |
| 95 | 28 | INV-100 | 539 | sakib vatty-lifan k19 | 0.00 | 1200.00 | ACTIVE |
| 96 | 147 | INV-100 | 536 | Motonext (Ohin) | 6000.00 | 0.00 | ACTIVE |
| 97 | 148 | INV-101 | 532 | mashfiq samit | 6450.00 | 0.00 | ACTIVE |
| 98 | 149 | INV-103 | 522 | Rabbi | 250.00 | 0.00 | ACTIVE |
| 99 | 30 | INV-106 | 526 | Chacha-Vatija Motors | 0.00 | 235.00 | ACTIVE |
| 100 | 152 | INV-106 | 534 | Shemanto | 4600.00 | 0.00 | ACTIVE |
| 101 | 31 | INV-108 | 517 | Rasel Vai-Savar | 0.00 | 1900.00 | DELETED |
| 102 | 32 | INV-109 | 511 | Aminul 4V-Tr | 1200.00 | 0.00 | DELETED |
| 103 | 57 | INV-109 | 511 | Aminul 4V-Tr | 1200.00 | 0.00 | ACTIVE |
| 104 | 153 | INV-109 | 574 | sujon Vai - MT 15 | 650.00 | 0.00 | ACTIVE |
| 105 | 33 | INV-110 | 541 | Mridul vai-Ledth Shop | 0.00 | 300.00 | DELETED |
| 106 | 158 | INV-110 | 530 | Bike Fix BD | 0.00 | 50.00 | ACTIVE |
| 107 | 34 | INV-111 | 520 | Sazal Vai | 0.00 | 500.00 | DELETED |
| 108 | 154 | INV-113 | 526 | Chacha-Vatija Motors | 1220.00 | 0.00 | ACTIVE |
| 109 | 155 | INV-114 | 526 | Chacha-Vatija Motors | 1500.00 | 0.00 | ACTIVE |
| 110 | 36 | INV-115 | 523 | Poran vai-FNF Motors | 0.00 | 500.00 | ACTIVE |
| 111 | 156 | INV-115 | 526 | Chacha-Vatija Motors | 360.00 | 0.00 | ACTIVE |
| 112 | 37 | INV-116 | 526 | Chacha-Vatija Motors | 0.00 | 1000.00 | ACTIVE |
| 113 | 157 | INV-116 | 526 | Chacha-Vatija Motors | 1100.00 | 0.00 | ACTIVE |
| 114 | 38 | INV-117 | 536 | Motonext (Ohin) | 0.00 | 780.00 | ACTIVE |
| 115 | 159 | INV-117 | 526 | Chacha-Vatija Motors | 800.00 | 0.00 | ACTIVE |
| 116 | 39 | INV-118 | 543 | Poran Vai-R15 V3 | -200.00 | 3050.00 | ACTIVE |
| 117 | 160 | INV-118 | 530 | Bike Fix BD | 200.00 | 0.00 | ACTIVE |
| 118 | 161 | INV-119 | 575 | Sifat Vai - R15 V4 | 0.00 | 2500.00 | ACTIVE |
| 119 | 40 | INV-120 | 523 | Poran vai-FNF Motors | 0.00 | 2850.00 | ACTIVE |
| 120 | 41 | INV-121 | 543 | Poran Vai-R15 V3 | 5200.00 | 0.00 | DELETED |
| 121 | 42 | INV-121 | 543 | Poran Vai-R15 V3 | 0.00 | 5200.00 | DELETED |
| 122 | 43 | INV-123 | 523 | Poran vai-FNF Motors | 0.00 | 1400.00 | ACTIVE |
| 123 | 59 | INV-126 | 534 | Shemanto | 0.00 | 4990.00 | ACTIVE |
| 124 | 162 | INV-126 | 532 | mashfiq samit | 4175.00 | 0.00 | ACTIVE |
| 125 | 44 | INV-127 | 511 | Aminul 4V-Tr | 250.00 | 0.00 | ACTIVE |
| 126 | 45 | INV-130 | 526 | Chacha-Vatija Motors | 0.00 | 35.00 | ACTIVE |
| 127 | 46 | INV-131 | 510 | Hamim Vai | 6400.00 | 0.00 | ACTIVE |
| 128 | 164 | INV-134 | 514 | Kabbo | 5150.00 | 0.00 | ACTIVE |
| 129 | 47 | INV-135 | 526 | Chacha-Vatija Motors | -6210.00 | 11650.00 | DELETED |
| 130 | 165 | INV-135 | 572 | Shihab Vai - R15 V2 | 12200.00 | 0.00 | DELETED |
| 131 | 166 | INV-135 | 572 | Shihab Vai - R15 V2 | 8600.00 | 0.00 | DELETED |
| 132 | 184 | INV-135 | 572 | Shihab Vai - R15 V2 | 8000.00 | 500.00 | ACTIVE |
| 133 | 48 | INV-139 | 522 | Rabbi | 0.00 | 2500.00 | ACTIVE |
| 134 | 49 | INV-140 | 510 | Hamim Vai | 1400.00 | 0.00 | DELETED |
| 135 | 172 | INV-140 | 522 | Rabbi | 10800.00 | 0.00 | DELETED |
| 136 | 177 | INV-140 | 522 | Rabbi | 2400.00 | 0.00 | ACTIVE |
| 137 | 50 | INV-141 | 547 | Rohan - R15 V3 | 420.00 | 0.00 | DELETED |
| 138 | 84 | INV-141 | 547 | Rohan - R15 V3 | 0.00 | 400.00 | ACTIVE |
| 139 | 173 | INV-141 | 578 | Imtiaz | 3200.00 | 0.00 | ACTIVE |
| 140 | 51 | INV-142 | 523 | Poran vai-FNF Motors | 0.00 | 5160.00 | DELETED |
| 141 | 175 | INV-142 | 536 | Motonext (Ohin) | -5460.00 | 0.00 | DELETED |
| 142 | 176 | INV-142 | 536 | Motonext (Ohin) | 540.00 | 0.00 | ACTIVE |
| 143 | 52 | INV-143 | 526 | Chacha-Vatija Motors | 60.00 | 0.00 | ACTIVE |
| 144 | 178 | INV-143 | 522 | Rabbi | 8700.00 | 0.00 | ACTIVE |
| 145 | 53 | INV-145 | 524 | Farhad Vai Kawla | 0.00 | 1550.00 | ACTIVE |
| 146 | 54 | INV-146 | 522 | Rabbi | -17450.00 | 19640.00 | ACTIVE |
| 147 | 179 | INV-146 | 540 | Al-amin vai- (TR rahat ) | 17450.00 | 0.00 | ACTIVE |
| 148 | 55 | INV-147 | 548 | sawon Quick Shifter | 420.00 | 0.00 | DELETED |
| 149 | 181 | INV-147 | 579 | Throttle Gear Bangladesh - TVS Rider | 6560.00 | 0.00 | DELETED |
| 150 | 185 | INV-147 | 579 | Throttle Gear Bangladesh - TVS Rider | 400.00 | 5800.00 | ACTIVE |
| 151 | 56 | INV-148 | 525 | TR Rahat Vai | 0.00 | 1250.00 | ACTIVE |
| 152 | 58 | INV-149 | 549 | Nabil-Tongi | 2200.00 | 0.00 | DELETED |
| 153 | 167 | INV-149 | 549 | Nabil-Tongi | 0.00 | 3000.00 | DELETED |
| 154 | 182 | INV-150 | 580 | jisan- r15 v2 (shihab vai) | 12200.00 | 0.00 | DELETED |
| 155 | 183 | INV-150 | 580 | jisan- r15 v2 (shihab vai) | 0.00 | 12100.00 | ACTIVE |
| 156 | 60 | INV-151 | 526 | Chacha-Vatija Motors | -3510.00 | 4200.00 | ACTIVE |
| 157 | 186 | INV-151 | 523 | Poran vai-FNF Motors | 2100.00 | 0.00 | ACTIVE |
| 158 | 187 | INV-152 | 523 | Poran vai-FNF Motors | 0.00 | 1150.00 | ACTIVE |
| 159 | 61 | INV-153 | 523 | Poran vai-FNF Motors | 450.00 | 0.00 | DELETED |
| 160 | 62 | INV-153 | 523 | Poran vai-FNF Motors | 200.00 | 0.00 | DELETED |
| 161 | 65 | INV-153 | 523 | Poran vai-FNF Motors | 200.00 | 0.00 | DELETED |
| 162 | 66 | INV-153 | 523 | Poran vai-FNF Motors | -1500.00 | 1700.00 | ACTIVE |
| 163 | 188 | INV-153 | 523 | Poran vai-FNF Motors | 1400.00 | 0.00 | ACTIVE |
| 164 | 63 | INV-154 | 523 | Poran vai-FNF Motors | 250.00 | 0.00 | DELETED |
| 165 | 64 | INV-154 | 523 | Poran vai-FNF Motors | 250.00 | 0.00 | DELETED |
| 166 | 78 | INV-154 | 523 | Poran vai-FNF Motors | -850.00 | 1100.00 | ACTIVE |
| 167 | 189 | INV-154 | 523 | Poran vai-FNF Motors | 1500.00 | 0.00 | ACTIVE |
| 168 | 67 | INV-155 | 532 | mashfiq samit | 0.00 | 5750.00 | ACTIVE |
| 169 | 68 | INV-156 | 532 | mashfiq samit | 1048.00 | 12522.00 | ACTIVE |
| 170 | 190 | INV-156 | 522 | Rabbi | 4000.00 | 0.00 | ACTIVE |
| 171 | 191 | INV-157 | 551 | rayhan vai - suzuki gixxer - rabbi | 50.00 | 0.00 | ACTIVE |
| 172 | 192 | INV-159 | 536 | Motonext (Ohin) | 750.00 | 0.00 | ACTIVE |
| 173 | 72 | INV-161 | 536 | Motonext (Ohin) | 0.00 | 8850.00 | ACTIVE |
| 174 | 73 | INV-162 | 534 | Shemanto | 0.00 | 950.00 | ACTIVE |
| 175 | 194 | INV-162 | 560 | sabbir pakuria - pulsar | 400.00 | 0.00 | ACTIVE |
| 176 | 74 | INV-165 | 541 | Mridul vai-Ledth Shop | 0.00 | 1000.00 | ACTIVE |
| 177 | 75 | INV-167 | 521 | Quick Shifter | 160.00 | 0.00 | ACTIVE |
| 178 | 76 | INV-168 | 552 | Uttara Bike Club | -280.00 | 450.00 | ACTIVE |
| 179 | 195 | INV-168 | 584 | Mahbub Alom - Yamaha - XSR | 450.00 | 0.00 | ACTIVE |
| 180 | 77 | INV-169 | 525 | TR Rahat Vai | 0.00 | 550.00 | ACTIVE |
| 181 | 196 | INV-169 | 540 | Al-amin vai- (TR rahat ) | 7960.00 | 0.00 | ACTIVE |
| 182 | 79 | INV-170 | 536 | Motonext (Ohin) | 910.00 | 0.00 | DELETED |
| 183 | 82 | INV-170 | 536 | Motonext (Ohin) | 0.00 | 910.00 | DELETED |
| 184 | 197 | INV-170 | 540 | Al-amin vai- (TR rahat ) | 3700.00 | 0.00 | DELETED |
| 185 | 199 | INV-170 | 540 | Al-amin vai- (TR rahat ) | 1600.00 | 0.00 | ACTIVE |
| 186 | 80 | INV-171 | 510 | Hamim Vai | -6960.00 | 7800.00 | ACTIVE |
| 187 | 198 | INV-171 | 525 | TR Rahat Vai | 7800.00 | 0.00 | ACTIVE |
| 188 | 81 | INV-172 | 510 | Hamim Vai | 3470.00 | 0.00 | ACTIVE |
| 189 | 83 | INV-173 | 536 | Motonext (Ohin) | 0.00 | 19230.00 | ACTIVE |
| 190 | 200 | INV-173 | 534 | Shemanto | 400.00 | 0.00 | ACTIVE |

---

## DIRECT CUSTOMER CONFLICTS — Complete List

The invoice counter reset on 2026-01-01 created two parallel series sharing the same invoice numbers (INV-1 through INV-173). Below are all conflicts where at least one side has a registered customer.

### CRITICAL — Both Series Have Different Registered Customers (70 conflicts)

These are the most dangerous: `customer_dues` records for one customer can get mixed with another's.

| #   | Invoice | Series | Sale ID | Date       | Customer                            | Grand Total | Paid    | Due      |
|-----|---------|--------|---------|------------|-------------------------------------|-------------|---------|----------|
| 1   | INV-2   | S1     | 2       | 2025-11-01 | Hamim Vai (510)                     | 1,200       | 1,200   | 0        |
|     |         | S2     | 185     | 2026-01-01 | Shovon Vai - NX-200 (555)           | 5,800       | 5,800   | 0        |
| 2   | INV-8   | S1     | 8       | 2025-11-04 | Aminul 4V-Tr (511)                  | 11,500      | 11,500  | 0        |
|     |         | S2     | 191     | 2026-01-05 | mahmud - R15 V3 (556)               | 1,500       | 0       | 1,500    |
| 3   | INV-11  | S1     | 11      | 2025-11-06 | Jawad Vai (512)                     | 13,700      | 13,700  | 0        |
|     |         | S2     | 194     | 2026-01-07 | wohi vai - Fzs V2 (557)             | 8,500       | 8,500   | 0        |
| 4   | INV-15  | S1     | 15      | 2025-11-10 | Kabbo (514)                         | 1,700       | 400     | 1,300    |
|     |         | S2     | 198     | 2026-01-04 | TR Rahat Vai (525)                  | 400         | 0       | 400      |
| 5   | INV-17  | S1     | 17      | 2025-11-10 | Rahamat Hosaain Joy (515)           | 0           | 0       | 0        |
|     |         | S2     | 200     | 2026-01-05 | top care (558)                      | 400         | 400     | 0        |
| 6   | INV-18  | S1     | 18      | 2025-11-10 | Nayem Vai (516)                     | 150         | 150     | 0        |
|     |         | S2     | 201     | 2026-01-05 | Quick Shifter (521)                 | 160         | 160     | 0        |
| 7   | INV-20  | S1     | 20      | 2025-11-11 | Rasel Vai-Savar (517)               | 5,900       | 5,900   | 0        |
|     |         | S2     | 203     | 2026-01-05 | Mridul vai-Ledth Shop (541)         | 100         | 0       | 100      |
| 8   | INV-21  | S1     | 21      | 2025-11-11 | Rasel Vai-Savar (517)               | 1,200       | 1,200   | 0        |
|     |         | S2     | 204     | 2026-01-06 | Mridul vai-Ledth Shop (541)         | 300         | 0       | 300      |
| 9   | INV-23  | S1     | 23      | 2025-11-12 | Fahim Vai (518)                     | 4,300       | 4,300   | 0        |
|     |         | S2     | 206     | 2026-01-08 | jibon technician (559)              | 460         | 460     | 0        |
| 10  | INV-27  | S1     | 27      | 2025-11-14 | Masud Vai (519)                     | 2,900       | 2,900   | 0        |
|     |         | S2     | 210     | 2026-01-04 | Poran vai-FNF Motors (523)          | 2,170       | 0       | 2,170    |
| 11  | INV-29  | S1     | 29      | 2025-11-14 | Sazal Vai (520)                     | 1,370       | 1,370   | 0        |
|     |         | S2     | 212     | 2026-01-09 | Poran vai-FNF Motors (523)          | 1,500       | 0       | 1,500    |
| 12  | INV-32  | S1     | 32      | 2025-11-16 | Kabbo (514)                         | 1,000       | 0       | 1,000    |
|     |         | S2     | 215     | 2026-01-06 | Bike Fix BD (530)                   | 150         | 150     | 0        |
| 13  | INV-33  | S1     | 33      | 2025-11-16 | Quick Shifter (521)                 | 450         | 450     | 0        |
|     |         | S2     | 216     | 2026-01-05 | Chacha-Vatija Motors (526)          | 750         | 0       | 750      |
| 14  | INV-36  | S1     | 36      | 2025-11-18 | Rabbi (522)                         | 20,650      | 23,530  | -2,780   |
|     |         | S2     | 219     | 2026-01-10 | Chacha-Vatija Motors (526)          | 560         | 0       | 560      |
| 15  | INV-42  | S1     | 42      | 2025-11-22 | Hamim Vai (510)                     | 24,060      | 31,860  | -7,800   |
|     |         | S2     | 225     | 2026-01-09 | Arif-RTR (542)                      | 7,800       | 0       | 7,800    |
| 16  | INV-43  | S1     | 43      | 2025-11-22 | Poran vai-FNF Motors (523)          | 1,400       | 1,400   | 0        |
|     |         | S2     | 226     | 2026-01-09 | Shuvo vai- R15 V3 Blue (563)        | 10,400      | 6,000   | 4,400    |
| 17  | INV-45  | S1     | 45      | 2025-11-22 | Farhad Vai Kawla (524)              | 2,100       | 2,100   | 0        |
|     |         | S2     | 228     | 2026-01-11 | Poran Vai-R15 V3 (543)              | 1,200       | 1,200   | 0        |
| 18  | INV-47  | S1     | 47      | 2025-11-23 | TR Rahat Vai (525)                  | 16,430      | 28,300  | -11,870  |
|     |         | S2     | 230     | 2026-01-12 | Dipto (564)                         | 67,800      | 35,000  | 32,800   |
| 19  | INV-48  | S1     | 48      | 2025-11-23 | Rabbi (522)                         | 8,400       | 10,700  | -2,300   |
|     |         | S2     | 231     | 2025-12-29 | rayhan vai - suzuki gixxer (551)    | 2,300       | 0       | 2,300    |
| 20  | INV-50  | S1     | 50      | 2025-11-24 | Aminul 4V-Tr (511)                  | 5,100       | 5,600   | -500     |
|     |         | S2     | 233     | 2026-01-11 | sakib vai - FZ-V2 (562)             | 500         | 0       | 500      |
| 21  | INV-55  | S1     | 55      | 2025-11-26 | Chacha-Vatija Motors (526)          | 1,620       | 1,620   | 0        |
|     |         | S2     | 238     | 2026-01-14 | Dipto (564)                         | 6,700       | 0       | 6,700    |
| 22  | INV-58  | S1     | 58      | 2025-11-26 | Hamim Vai (510)                     | 1,330       | 2,430   | -1,100   |
|     |         | S2     | 241     | 2026-01-14 | rayhan vai - suzuki gixxer (551)    | 1,150       | 0       | 1,150    |
| 23  | INV-59  | S1     | 59      | 2025-11-26 | khan masum vai (527)                | 14,750      | 14,500  | 250      |
|     |         | S2     | 242     | 2026-01-14 | Poran Vai-R15 V3 (543)              | 500         | 0       | 500      |
| 24  | INV-60  | S1     | 60      | 2025-11-26 | raj vai (528)                       | 11,800      | 13,900  | -2,100   |
|     |         | S2     | 243     | 2026-01-14 | Fahim Vai (518)                     | 2,100       | 0       | 2,100    |
| 25  | INV-66  | S1     | 66      | 2025-11-29 | Mahabub Sir (531)                   | 3,280       | 4,030   | -750     |
|     |         | S2     | 249     | 2026-01-13 | Motonext (Ohin) (536)               | 750         | 0       | 750      |
| 26  | INV-67  | S1     | 67      | 2025-11-30 | Bike Fix BD (530)                   | 1,200       | 1,800   | -600     |
|     |         | S2     | 250     | 2026-01-14 | Motonext (Ohin) (536)               | 600         | 0       | 600      |
| 27  | INV-69  | S1     | 69      | 2025-11-30 | mashfiq samit (532)                 | 8,748       | 8,748   | 0        |
|     |         | S2     | 252     | 2026-01-12 | Chacha-Vatija Motors (526)          | 560         | 560     | 0        |
| 28  | INV-73  | S1     | 73      | 2025-11-30 | Shemanto (534)                      | 300         | 300     | 0        |
|     |         | S2     | 256     | 2026-01-15 | Bike Fix BD (530)                   | 570         | 570     | 0        |
| 29  | INV-74  | S1     | 74      | 2025-11-30 | mashfiq samit (532)                 | 300         | 300     | 0        |
|     |         | S2     | 257     | 2026-01-14 | fahad vai - TVS 4V (566)            | 1,600       | 1,600   | 0        |
| 30  | INV-76  | S1     | 76      | 2025-11-30 | TR Rahat Vai (525)                  | 350         | 350     | 0        |
|     |         | S2     | 259     | 2026-01-15 | mahmud - R15 V3 (556)               | 950         | 0       | 950      |
| 31  | INV-77  | S1     | 77      | 2025-11-30 | Shop Bike (535)                     | 1,320       | 1,320   | 0        |
|     |         | S2     | 260     | 2026-01-15 | Jilani vai - R15 V3 (567)           | 7,100       | 7,100   | 0        |
| 32  | INV-82  | S1     | 82      | 2025-12-02 | Chacha-Vatija Motors (526)          | 80          | 80      | 0        |
|     |         | S2     | 265     | 2026-01-16 | sayed uncle - Discover 125 (550)    | 800         | 800     | 0        |
| 33  | INV-84  | S1     | 84      | 2025-12-03 | Motonext (Ohin) (536)               | 4,250       | 4,250   | 0        |
|     |         | S2     | 267     | 2026-01-16 | Hamim Vai (510)                     | 1,450       | 0       | 1,450    |
| 34  | INV-86  | S1     | 86      | 2025-12-04 | Farhad Vai Kawla (524)              | 9,100       | 9,100   | 0        |
|     |         | S2     | 271     | 2026-01-16 | Chacha-Vatija Motors (526)          | 400         | 0       | 400      |
| 35  | INV-88  | S1     | 88      | 2025-12-04 | Poran vai-FNF Motors (523)          | 1,800       | 1,800   | 0        |
|     |         | S2     | 273     | 2026-01-17 | sawan vai - Gixxer (570)            | 2,100       | 900     | 1,200    |
| 36  | INV-93  | S1     | 93      | 2025-12-07 | Jawad Vai (512)                     | 1,200       | 47,500  | -46,300  |
|     |         | S2     | 278     | 2026-01-19 | Rabbi (522)                         | 126,300     | 80,000  | 46,300   |
| 37  | INV-94  | S1     | 94      | 2025-12-07 | Motonext (Ohin) (536)               | 5,040       | 17,260  | -12,220  |
|     |         | S2     | 279     | 2026-01-19 | Rabbi (522)                         | 5,050       | 0       | 5,050    |
| 38  | INV-96  | S1     | 96      | 2025-12-07 | Al-amin vai (540)                   | 7,400       | 7,400   | 0        |
|     |         | S2     | 281     | 2026-01-19 | mahmud - R15 V3 (556)               | 12,000      | 4,000   | 8,000    |
| 39  | INV-98  | S1     | 98      | 2025-12-08 | Poran vai-FNF Motors (523)          | 800         | 800     | 0        |
|     |         | S2     | 283     | 2026-01-19 | mashfiq samit (532)                 | 4,720       | 4,720   | 0        |
| 40  | INV-100 | S1     | 100     | 2025-12-08 | sakib vatty-lifan k19 (539)         | 2,700       | 2,700   | 0        |
|     |         | S2     | 285     | 2026-01-21 | Motonext (Ohin) (536)               | 6,000       | 0       | 6,000    |
| 41  | INV-102 | S1     | 102     | 2025-12-09 | Rabbi (522)                         | 530         | 530     | 0        |
|     |         | S2     | 287     | 2026-01-20 | Jawad Vai (512)                     | 200         | 200     | 0        |
| 42  | INV-103 | S1     | 103     | 2025-12-09 | Nayem Vai (516)                     | 700         | 700     | 0        |
|     |         | S2     | 288     | 2026-01-21 | Rabbi (522)                         | 250         | 0       | 250      |
| 43  | INV-106 | S1     | 106     | 2025-12-10 | Chacha-Vatija Motors (526)          | 235         | 235     | 0        |
|     |         | S2     | 291     | 2026-01-21 | Shemanto (534)                      | 5,600       | 1,000   | 4,600    |
| 44  | INV-109 | S1     | 109     | 2025-12-11 | Aminul 4V-Tr (511)                  | 1,200       | 0       | 1,200    |
|     |         | S2     | 294     | 2026-01-21 | sujon Vai - MT 15 (574)             | 1,650       | 1,000   | 650      |
| 45  | INV-110 | S1     | 110     | 2025-12-11 | Mridul vai-Ledth Shop (541)         | 300         | 350     | -50      |
|     |         | S2     | 295     | 2026-01-21 | Bike Fix BD (530)                   | 2,250       | 2,200   | 50       |
| 46  | INV-114 | S1     | 114     | 2025-12-13 | Arif-RTR (542)                      | 7,300       | 7,300   | 0        |
|     |         | S2     | 299     | 2026-01-18 | Chacha-Vatija Motors (526)          | 1,500       | 0       | 1,500    |
| 47  | INV-115 | S1     | 115     | 2025-12-13 | Poran vai-FNF Motors (523)          | 500         | 500     | 0        |
|     |         | S2     | 300     | 2026-01-19 | Chacha-Vatija Motors (526)          | 360         | 0       | 360      |
| 48  | INV-117 | S1     | 117     | 2025-12-13 | Motonext (Ohin) (536)               | 780         | 780     | 0        |
|     |         | S2     | 302     | 2026-01-23 | Chacha-Vatija Motors (526)          | 800         | 0       | 800      |
| 49  | INV-118 | S1     | 118     | 2025-12-13 | Poran Vai-R15 V3 (543)              | 2,850       | 3,050   | -200     |
|     |         | S2     | 303     | 2026-01-23 | Bike Fix BD (530)                   | 200         | 0       | 200      |
| 50  | INV-120 | S1     | 120     | 2025-12-14 | Poran vai-FNF Motors (523)          | 2,850       | 2,850   | 0        |
|     |         | S2     | 305     | 2026-01-23 | FZ V3 (576)                         | 4,250       | 4,250   | 0        |
| 51  | INV-126 | S1     | 126     | 2025-12-18 | Shemanto (534)                      | 4,990       | 4,990   | 0        |
|     |         | S2     | 311     | 2026-01-24 | mashfiq samit (532)                 | 4,175       | 0       | 4,175    |
| 52  | INV-135 | S1     | 135     | 2025-12-22 | Chacha-Vatija Motors (526)          | 5,440       | 12,150  | -6,710   |
|     |         | S2     | 321     | 2026-01-27 | Shihab Vai - R15 V2 (572)           | 10,500      | 2,000   | 8,500    |
| 53  | INV-139 | S1     | 139     | 2025-12-23 | Rabbi (522)                         | 2,500       | 2,500   | 0        |
|     |         | S2     | 325     | 2026-01-28 | Bike Fix BD (530)                   | 5,030       | 5,030   | 0        |
| 54  | INV-140 | S1     | 140     | 2025-12-23 | Hamim Vai (510)                     | 1,400       | 0       | 1,400    |
|     |         | S2     | 326     | 2026-01-24 | Rabbi (522)                         | 2,400       | 0       | 2,400    |
| 55  | INV-141 | S1     | 141     | 2025-12-23 | Rohan - R15 V3 (547)                | 400         | 400     | 0        |
|     |         | S2     | 327     | 2026-01-29 | Imtiaz (578)                        | 3,300       | 100     | 3,200    |
| 56  | INV-142 | S1     | 142     | 2025-12-23 | Poran vai-FNF Motors (523)          | 5,160       | 5,160   | 0        |
|     |         | S2     | 328     | 2026-01-29 | Motonext (Ohin) (536)               | 540         | 0       | 540      |
| 57  | INV-143 | S1     | 143     | 2025-12-23 | Chacha-Vatija Motors (526)          | 60          | 0       | 60       |
|     |         | S2     | 329     | 2026-01-29 | Rabbi (522)                         | 8,700       | 0       | 8,700    |
| 58  | INV-146 | S1     | 146     | 2025-12-24 | Rabbi (522)                         | 2,190       | 19,640  | -17,450  |
|     |         | S2     | 332     | 2026-01-28 | Al-amin vai (540)                   | 17,450      | 0       | 17,450   |
| 59  | INV-147 | S1     | 147     | 2025-12-24 | sawon Quick Shifter (548)           | 420         | 5,800   | -5,380   |
|     |         | S2     | 333     | 2026-01-29 | Throttle Gear Bangladesh (579)      | 6,200       | 0       | 6,200    |
| 60  | INV-151 | S1     | 151     | 2025-12-24 | Chacha-Vatija Motors (526)          | 690         | 4,200   | -3,510   |
|     |         | S2     | 337     | 2026-01-21 | Poran vai-FNF Motors (523)          | 2,100       | 0       | 2,100    |
| 61  | INV-155 | S1     | 155     | 2025-12-29 | mashfiq samit (532)                 | 5,750       | 5,750   | 0        |
|     |         | S2     | 341     | 2026-01-31 | Chacha-Vatija Motors (526)          | 1,780       | 1,780   | 0        |
| 62  | INV-156 | S1     | 156     | 2025-12-29 | mashfiq samit (532)                 | 13,570      | 12,522  | 1,048    |
|     |         | S2     | 342     | 2026-01-31 | Rabbi (522)                         | 4,000       | 0       | 4,000    |
| 63  | INV-161 | S1     | 161     | 2025-12-29 | Motonext (Ohin) (536)               | 8,850       | 8,850   | 0        |
|     |         | S2     | 347     | 2026-02-02 | forhad vai (582)                    | 2,800       | 2,800   | 0        |
| 64  | INV-162 | S1     | 162     | 2025-12-31 | Shemanto (534)                      | 2,950       | 2,950   | 0        |
|     |         | S2     | 348     | 2026-02-02 | sabbir pakuria - pulsar (560)       | 900         | 500     | 400      |
| 65  | INV-165 | S1     | 165     | 2025-12-26 | Mridul vai-Ledth Shop (541)         | 1,000       | 1,000   | 0        |
|     |         | S2     | 351     | 2026-02-02 | JOY Vai Market - R15 V3 (583)       | 2,400       | 2,400   | 0        |
| 66  | INV-168 | S1     | 168     | 2025-12-30 | Uttara Bike Club (552)              | 170         | 450     | -280     |
|     |         | S2     | 354     | 2026-02-03 | Mahbub Alom - Yamaha - XSR (584)    | 6,650       | 6,200   | 450      |
| 67  | INV-169 | S1     | 169     | 2025-12-31 | TR Rahat Vai (525)                  | 550         | 550     | 0        |
|     |         | S2     | 355     | 2026-02-04 | Al-amin vai (540)                   | 7,960       | 0       | 7,960    |
| 68  | INV-170 | S1     | 170     | 2025-12-31 | Motonext (Ohin) (536)               | 910         | 910     | 0        |
|     |         | S2     | 356     | 2026-02-04 | Al-amin vai (540)                   | 1,600       | 0       | 1,600    |
| 69  | INV-171 | S1     | 171     | 2025-12-30 | Hamim Vai (510)                     | 840         | 7,800   | -6,960   |
|     |         | S2     | 357     | 2026-02-04 | TR Rahat Vai (525)                  | 8,800       | 1,000   | 7,800    |
| 70  | INV-173 | S1     | 173     | 2025-12-03 | Motonext (Ohin) (536)               | 19,230      | 19,230  | 0        |
|     |         | S2     | 359     | 2026-02-04 | Shemanto (534)                      | 3,400       | 3,000   | 400      |

### CRITICAL — Both Series Have the SAME Registered Customer (5 conflicts)

These cause double-counting in `customer_dues` but no cross-customer contamination.

| #   | Invoice  | Series | Sale ID | Date       | Customer                            | Grand Total | Due      |
|-----|----------|--------|---------|------------|-------------------------------------|-------------|----------|
| 1   | INV-65   | S1     | 65      | 2025-11-29 | Poran vai-FNF Motors (523)          | 5,400       | -600     |
|     |          | S2     | 248     | 2026-01-15 |                                     | 600         | 600      |
| 2   | INV-80   | S1     | 80      | 2025-12-01 | Poran vai-FNF Motors (523)          | 1,230       | -9,800   |
|     |          | S2     | 263     | 2026-01-16 |                                     | 1,400       | 1,400    |
| 3   | INV-116  | S1     | 116     | 2025-12-13 | Chacha-Vatija Motors (526)          | 1,000       | 0        |
|     |          | S2     | 301     | 2026-01-21 |                                     | 1,100       | 1,100    |
| 4   | INV-153  | S1     | 153     | 2025-12-29 | Poran vai-FNF Motors (523)          | 200         | -1,500   |
|     |          | S2     | 339     | 2026-01-25 |                                     | 1,400       | 1,400    |
| 5   | INV-154  | S1     | 154     | 2025-12-28 | Poran vai-FNF Motors (523)          | 250         | -850     |
|     |          | S2     | 340     | 2026-01-31 |                                     | 1,500       | 1,500    |

### HIGH — One Registered Customer + One Guest (71 conflicts)

| #   | Invoice | Series | Sale ID | Date       | Customer                            | Grand Total |
|-----|---------|--------|---------|------------|-------------------------------------|-------------|
| 1   | INV-4   | S1     | 4       | 2025-11-02 | Guest                               | 17,380      |
|     |         | S2     | 187     | 2026-01-02 | Sazal Vai (520)                     | 800         |
| 2   | INV-13  | S1     | 13      | 2025-11-10 | Guest                               | 5,530       |
|     |         | S2     | 196     | 2025-12-13 | badal ali (503)                     | 2,050       |
| 3   | INV-14  | S1     | 14      | 2025-11-10 | Rahul Vai (513)                     | 1,450       |
|     |         | S2     | 197     | 2026-01-08 | Guest                               | 1,660       |
| 4   | INV-16  | S1     | 16      | 2025-11-10 | Guest                               | 1,100       |
|     |         | S2     | 199     | 2026-01-03 | Mridul vai-Ledth Shop (541)         | 1,000       |
| 5   | INV-19  | S1     | 19      | 2025-11-11 | Guest                               | 12,590      |
|     |         | S2     | 202     | 2026-01-05 | jibon technician (559)              | 350         |
| 6   | INV-22  | S1     | 22      | 2025-11-12 | Guest                               | 2,150       |
|     |         | S2     | 205     | 2026-01-07 | sabbir pakuria (560)                | 2,900       |
| 7   | INV-25  | S1     | 25      | 2025-11-13 | Guest                               | 3,710       |
|     |         | S2     | 208     | 2025-12-09 | jibon technician (559)              | 600         |
| 8   | INV-26  | S1     | 26      | 2025-11-13 | Guest                               | 5,400       |
|     |         | S2     | 209     | 2026-01-03 | Poran vai-FNF Motors (523)          | 3,600       |
| 9   | INV-28  | S1     | 28      | 2025-11-14 | Guest                               | 10,300      |
|     |         | S2     | 211     | 2026-01-05 | Poran vai-FNF Motors (523)          | 1,000       |
| 10  | INV-30  | S1     | 30      | 2025-11-15 | Guest                               | 8,270       |
|     |         | S2     | 213     | 2026-01-10 | Poran vai-FNF Motors (523)          | 1,710       |
| 11  | INV-31  | S1     | 31      | 2025-11-16 | Guest                               | 14,830      |
|     |         | S2     | 214     | 2026-01-08 | Motonext (Ohin) (536)               | 1,530       |
| 12  | INV-34  | S1     | 34      | 2025-11-17 | Guest                               | 18,150      |
|     |         | S2     | 217     | 2026-01-06 | Chacha-Vatija Motors (526)          | 2,320       |
| 13  | INV-35  | S1     | 35      | 2025-11-18 | Guest                               | 1,320       |
|     |         | S2     | 218     | 2026-01-09 | Chacha-Vatija Motors (526)          | 1,750       |
| 14  | INV-37  | S1     | 37      | 2025-11-19 | Guest                               | 11,190      |
|     |         | S2     | 220     | 2026-01-06 | Shemanto (534)                      | 2,880       |
| 15  | INV-38  | S1     | 38      | 2025-11-20 | Guest                               | 1,170       |
|     |         | S2     | 221     | 2026-01-08 | Rifat Shohid (561)                  | 4,150       |
| 16  | INV-40  | S1     | 40      | 2025-11-22 | Jawad Vai (512)                     | 16,810      |
|     |         | S2     | 223     | 2026-01-10 | Guest                               | 13,780      |
| 17  | INV-41  | S1     | 41      | 2025-11-22 | Guest                               | 850         |
|     |         | S2     | 224     | 2026-01-10 | sakib vai (562)                     | 5,800       |
| 18  | INV-46  | S1     | 46      | 2025-11-23 | Guest                               | 20,490      |
|     |         | S2     | 229     | 2026-01-11 | Poran vai-FNF Motors (523)          | 1,050       |
| 19  | INV-49  | S1     | 49      | 2025-11-24 | Guest                               | 7,260       |
|     |         | S2     | 232     | 2026-01-11 | Dipto (564)                         | 1,200       |
| 20  | INV-52  | S1     | 52      | 2025-11-25 | Hamim Vai (510)                     | 3,440       |
|     |         | S2     | 235     | 2026-01-12 | Guest                               | 11,140      |
| 21  | INV-54  | S1     | 54      | 2025-11-26 | Poran vai-FNF Motors (523)          | 1,360       |
|     |         | S2     | 237     | 2026-01-14 | Guest                               | 4,680       |
| 22  | INV-56  | S1     | 56      | 2025-11-26 | Guest                               | 5,350       |
|     |         | S2     | 239     | 2026-01-14 | sakib vai (562)                     | 800         |
| 23  | INV-57  | S1     | 57      | 2025-11-26 | Guest                               | 700         |
|     |         | S2     | 240     | 2026-01-14 | Aminul 4V-Tr (511)                  | 8,000       |
| 24  | INV-61  | S1     | 61      | 2025-11-26 | Guest                               | 3,200       |
|     |         | S2     | 244     | 2026-01-11 | Al-Amin Vai - BH (565)              | 850         |
| 25  | INV-62  | S1     | 62      | 2025-11-27 | Guest                               | 9,800       |
|     |         | S2     | 245     | 2026-01-15 | Al-Amin Vai - BH (565)              | 1,850       |
| 26  | INV-63  | S1     | 63      | 2025-11-28 | Guest                               | 20,400      |
|     |         | S2     | 246     | 2026-01-13 | Poran vai-FNF Motors (523)          | 850         |
| 27  | INV-64  | S1     | 64      | 2025-11-29 | Guest                               | 8,160       |
|     |         | S2     | 247     | 2026-01-14 | Poran vai-FNF Motors (523)          | 1,100       |
| 28  | INV-68  | S1     | 68      | 2025-11-30 | Guest                               | 16,090      |
|     |         | S2     | 251     | 2026-01-15 | Motonext (Ohin) (536)               | 1,900       |
| 29  | INV-70  | S1     | 70      | 2025-11-30 | Guest                               | 4,200       |
|     |         | S2     | 253     | 2026-01-13 | Chacha-Vatija Motors (526)          | 1,230       |
| 30  | INV-71  | S1     | 71      | 2025-11-30 | Guest                               | 3,100       |
|     |         | S2     | 254     | 2026-01-14 | Chacha-Vatija Motors (526)          | 630         |
| 31  | INV-72  | S1     | 72      | 2025-11-30 | TR Rahat Vai (525)                  | 4,000       |
|     |         | S2     | 255     | 2026-01-15 | Guest                               | 14,350      |
| 32  | INV-75  | S1     | 75      | 2025-11-30 | Guest                               | 150         |
|     |         | S2     | 258     | 2026-01-15 | Al-amin vai (540)                   | 3,100       |
| 33  | INV-85  | S1     | 85      | 2025-12-04 | Guest                               | 9,110       |
|     |         | S2     | 270     | 2026-01-17 | Chacha-Vatija Motors (526)          | 600         |
| 34  | INV-87  | S1     | 87      | 2025-12-04 | Motonext (Ohin) (536)               | 14,460      |
|     |         | S2     | 272     | 2026-01-17 | Guest                               | 3,880       |
| 35  | INV-89  | S1     | 89      | 2025-12-05 | Guest                               | 14,440      |
|     |         | S2     | 274     | 2026-01-17 | Galib Vai (571)                     | 4,700       |
| 36  | INV-91  | S1     | 91      | 2025-12-06 | Hamim Vai (510)                     | 10,450      |
|     |         | S2     | 276     | 2026-01-18 | Guest                               | 3,150       |
| 37  | INV-92  | S1     | 92      | 2025-12-07 | Guest                               | 17,550      |
|     |         | S2     | 277     | 2026-01-19 | Poran vai-FNF Motors (523)          | 3,900       |
| 38  | INV-95  | S1     | 95      | 2025-12-07 | siyam vai(ohin) (538)               | 3,190       |
|     |         | S2     | 280     | 2026-01-19 | Guest                               | 3,300       |
| 39  | INV-97  | S1     | 97      | 2025-12-08 | Guest                               | 21,070      |
|     |         | S2     | 282     | 2026-01-19 | rayhan vai (551)                    | 5,650       |
| 40  | INV-99  | S1     | 99      | 2025-12-08 | Motonext (Ohin) (536)               | 2,210       |
|     |         | S2     | 284     | 2026-01-20 | Guest                               | 770         |
| 41  | INV-101 | S1     | 101     | 2025-12-09 | Guest                               | 2,670       |
|     |         | S2     | 286     | 2026-01-20 | mashfiq samit (532)                 | 7,450       |
| 42  | INV-108 | S1     | 108     | 2025-12-11 | Rasel Vai-Savar (517)               | 1,900       |
|     |         | S2     | 293     | 2026-01-21 | Guest                               | 4,850       |
| 43  | INV-111 | S1     | 111     | 2025-12-11 | Sazal Vai (520)                     | 500         |
|     |         | S2     | 296     | 2026-01-22 | Guest                               | 16,080      |
| 44  | INV-112 | S1     | 112     | 2025-12-12 | Guest                               | 2,060       |
|     |         | S2     | 297     | 2026-01-22 | Galib Vai (571)                     | 3,250       |
| 45  | INV-113 | S1     | 113     | 2025-12-13 | Guest                               | 3,700       |
|     |         | S2     | 298     | 2026-01-17 | Chacha-Vatija Motors (526)          | 1,220       |
| 46  | INV-119 | S1     | 119     | 2025-12-14 | Guest                               | 2,600       |
|     |         | S2     | 304     | 2026-01-23 | Sifat Vai - R15 V4 (575)            | 13,000      |
| 47  | INV-121 | S1     | 121     | 2025-12-15 | Poran Vai-R15 V3 (543)              | 5,200       |
|     |         | S2     | 306     | 2026-01-23 | Guest                               | 6,300       |
| 48  | INV-123 | S1     | 123     | 2025-12-16 | Poran vai-FNF Motors (523)          | 1,400       |
|     |         | S2     | 308     | 2026-01-24 | Guest                               | 5,320       |
| 49  | INV-124 | S1     | 124     | 2025-12-17 | Shanto (544)                        | 7,600       |
|     |         | S2     | 309     | 2026-01-24 | Guest                               | 3,250       |
| 50  | INV-125 | S1     | 125     | 2025-12-18 | Guest                               | 550         |
|     |         | S2     | 310     | 2026-01-24 | mashfiq samit (532)                 | 5,400       |
| 51  | INV-127 | S1     | 127     | 2025-12-18 | Aminul 4V-Tr (511)                  | 2,150       |
|     |         | S2     | 312     | 2026-01-24 | Guest                               | 1,100       |
| 52  | INV-129 | S1     | 129     | 2025-12-19 | Aminul 4V-Tr (511)                  | 2,800       |
|     |         | S2     | 314     | 2026-01-25 | Guest                               | 1,500       |
| 53  | INV-130 | S1     | 130     | 2025-12-19 | Chacha-Vatija Motors (526)          | 1,320       |
|     |         | S2     | 315     | 2026-01-25 | Guest                               | 800         |
| 54  | INV-131 | S1     | 131     | 2025-12-19 | Hamim Vai (510)                     | 6,400       |
|     |         | S2     | 316     | 2026-01-26 | Guest                               | 12,620      |
| 55  | INV-132 | S1     | 132     | 2025-12-20 | Guest                               | 1,180       |
|     |         | S2     | 318     | 2026-01-26 | Jawad Vai (512)                     | 1,600       |
| 56  | INV-133 | S1     | 133     | 2025-12-22 | Guest                               | 5,450       |
|     |         | S2     | 319     | 2026-01-26 | fahad vai - TVS 4V (566)            | 3,000       |
| 57  | INV-134 | S1     | 134     | 2025-12-21 | Guest                               | 11,850      |
|     |         | S2     | 320     | 2026-01-26 | Kabbo (514)                         | 6,150       |
| 58  | INV-136 | S1     | 136     | 2025-12-22 | laden vai - gixxer (545)            | 9,500       |
|     |         | S2     | 322     | 2026-01-27 | Guest                               | 6,500       |
| 59  | INV-137 | S1     | 137     | 2025-12-22 | taimur vai (546)                    | 20,000      |
|     |         | S2     | 323     | 2026-01-28 | Guest                               | 16,860      |
| 60  | INV-138 | S1     | 138     | 2025-12-23 | Guest                               | 9,200       |
|     |         | S2     | 324     | 2026-01-28 | turan vai (577)                     | 4,700       |
| 61  | INV-145 | S1     | 145     | 2025-12-24 | Farhad Vai Kawla (524)              | 1,550       |
|     |         | S2     | 331     | 2026-01-29 | Guest                               | 450         |
| 62  | INV-148 | S1     | 148     | 2025-12-24 | TR Rahat Vai (525)                  | 1,250       |
|     |         | S2     | 334     | 2026-01-30 | Guest                               | 560         |
| 63  | INV-149 | S1     | 149     | 2025-12-13 | Nabil-Tongi (549)                   | 8,000       |
|     |         | S2     | 335     | 2026-01-30 | Guest                               | 900         |
| 64  | INV-150 | S1     | 150     | 2025-12-25 | Guest                               | 11,660      |
|     |         | S2     | 336     | 2026-01-30 | jisan- r15 v2 (580)                 | 12,100      |
| 65  | INV-152 | S1     | 152     | 2025-12-26 | Guest                               | 9,250       |
|     |         | S2     | 338     | 2026-01-22 | Poran vai-FNF Motors (523)          | 1,150       |
| 66  | INV-157 | S1     | 157     | 2025-12-28 | Guest                               | 6,620       |
|     |         | S2     | 343     | 2026-01-31 | rayhan vai (551)                    | 850         |
| 67  | INV-158 | S1     | 158     | 2025-12-28 | sayed uncle (550)                   | 4,900       |
|     |         | S2     | 344     | 2026-01-31 | Guest                               | 100         |
| 68  | INV-159 | S1     | 159     | 2025-12-29 | Guest                               | 8,030       |
|     |         | S2     | 345     | 2026-02-02 | Motonext (Ohin) (536)               | 750         |
| 69  | INV-164 | S1     | 164     | 2025-12-25 | Bike Fix BD (530)                   | 1,950       |
|     |         | S2     | 350     | 2026-02-02 | Guest                               | 8,650       |
| 70  | INV-167 | S1     | 167     | 2025-12-29 | Quick Shifter (521)                 | 160         |
|     |         | S2     | 353     | 2026-02-03 | Guest                               | 3,700       |
| 71  | INV-172 | S1     | 172     | 2025-12-31 | Hamim Vai (510)                     | 3,470       |
|     |         | S2     | 358     | 2026-02-04 | Guest                               | 2,450       |

### Most Affected Customers

| Customer             | ID  | Appears in CRITICAL Conflicts |
|----------------------|-----|-------------------------------|
| Poran vai-FNF Motors | 523 | 16 times                      |
| Chacha-Vatija Motors | 526 | 14 times                      |
| Motonext (Ohin)      | 536 | 10 times                      |
| Rabbi                | 522 | 9 times                       |
| Hamim Vai            | 510 | 5 times                       |
| TR Rahat Vai         | 525 | 5 times                       |
| Shemanto             | 534 | 5 times                       |
| mashfiq samit        | 532 | 5 times                       |
| Aminul 4V-Tr         | 511 | 4 times                       |
| Bike Fix BD          | 530 | 4 times                       |
| Al-amin vai          | 540 | 4 times                       |
| Dipto                | 564 | 3 times                       |
| Kabbo                | 514 | 3 times                       |
| Farhad Vai Kawla     | 524 | 3 times                       |

### Highest-Value Conflicts

| Invoice | Total Amount at Risk          | Customers Affected              |
|---------|-------------------------------|---------------------------------|
| INV-93  | 126,300 (S2) / 1,200 (S1)     | Jawad Vai vs Rabbi              |
| INV-47  | 67,800 (S2) / 16,430 (S1)     | TR Rahat Vai vs Dipto           |
| INV-42  | 24,060 (S1) / 7,800 (S2)      | Hamim Vai vs Arif-RTR           |
| INV-36  | 20,650 (S1) / 560 (S2)        | Rabbi vs Chacha-Vatija          |
| INV-173 | 19,230 (S1) / 3,400 (S2)      | Motonext vs Shemanto            |
| INV-146 | 17,450 (S2) / 2,190 (S1)      | Rabbi vs Al-amin vai            |
| INV-59  | 14,750 (S1) / 500 (S2)        | khan masum vai vs Poran Vai-R15 |
| INV-60  | 11,800 (S1) / 2,100 (S2)      | raj vai vs Fahim Vai            |
| INV-135 | 10,500 (S2) / 5,440 (S1)      | Chacha-Vatija vs Shihab Vai     |
| INV-43  | 10,400 (S2) / 1,400 (S1)      | Poran vai-FNF vs Shuvo vai      |

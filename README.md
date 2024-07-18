A Kecso Depo alkalmazás célja a depó működésének kezelése, különös tekintettel a jármű- és futárkezelésre. Az alkalmazás lehetőséget biztosít járművek és futárok hozzáadására, frissítésére, törlésére, valamint a költségek és szállítások nyomon követésére.

## Funkciók

- **Járműkezelés**: Járművek hozzáadása, frissítése, törlése és megjelenítése.
- **Futárkezelés**: Futárok hozzáadása, frissítése, törlése és megjelenítése.
- **Költségkezelés**: A járművek fenntartási költségeinek nyomon követése.
- **Szállításkezelés**: Szállítások hozzáadása, nyomon követése és jelentések készítése.

## Telepítés

1. Klónozd a repót:
    ```sh
    git clone https://github.com/felhasznalo/kecso-depo.git
    ```

2. Navigálj a projekt könyvtárába:
    ```sh
    cd kecso-depo
    ```

3. Telepítsd a szükséges függőségeket:
    ```sh
    pip install -r requirements.txt
    ```

## Használat

1. Indítsd el az alkalmazást:
    ```sh
    python app.py
    ```

2. Nyisd meg a böngésződben a következő URL-t:
    ```
    http://localhost:5000
    ```

## API Végpontok

### Járműkezelés

- **Új jármű hozzáadása**
    ```http
    POST /vehicles
    ```
    - Paraméterek: `make`, `model`, `year`, `license_plate`
    - Válasz: Az újonnan létrehozott jármű adatai

- **Jármű frissítése**
    ```http
    PUT /vehicles/{id}
    ```
    - Paraméterek: `make`, `model`, `year`, `license_plate`
    - Válasz: A frissített jármű adatai

- **Jármű törlése**
    ```http
    DELETE /vehicles/{id}
    ```
    - Válasz: Törlés visszaigazolása

### Futárkezelés

- **Új futár hozzáadása**
    ```http
    POST /couriers
    ```
    - Paraméterek: `name`, `email`, `phone`
    - Válasz: Az újonnan létrehozott futár adatai

- **Futár frissítése**
    ```http
    PUT /couriers/{id}
    ```
    - Paraméterek: `name`, `email`, `phone`
    - Válasz: A frissített futár adatai

- **Futár törlése**
    ```http
    DELETE /couriers/{id}
    ```
    - Válasz: Törlés visszaigazolása

## Közreműködés

Szívesen várunk minden közreműködést! Kérjük, kövessétek az alábbi lépéseket:

1. Forkoljátok a repót.
2. Hozzatok létre egy új ágat (`git checkout -b feature/uj-funkcio`).
3. Commiteljétek a változtatásokat (`git commit -m 'Új funkció hozzáadása'`).
4. Pusholjátok az ágat (`git push origin feature/uj-funkcio`).
5. Készítsetek egy Pull Request-et.

## Licenc

Ez a projekt a [MIT Licenc](LICENSE) alatt érhető el.
 

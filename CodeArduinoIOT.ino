#include <WiFi.h>
#include <AsyncTCP.h>
#include <WiFiManager.h>
#include <ESPAsyncWebServer.h>
#include <Adafruit_GFX.h> 
#include <Adafruit_NeoMatrix.h> 
#include <Adafruit_NeoPixel.h> 

#ifndef PSTR 
#define PSTR 
#endif 
#define PIN 33
#define LED_PIN 26

#define STARTV 0
#define END_V 4096
#define FADE_DELAY 50

////////////// VARIABLES ////////////////////////
Adafruit_NeoMatrix matrix = Adafruit_NeoMatrix(16, 8, PIN, NEO_MATRIX_BOTTOM + NEO_MATRIX_LEFT + NEO_MATRIX_COLUMNS, NEO_GRB + NEO_KHZ800);
Adafruit_NeoMatrix matrix1 = Adafruit_NeoMatrix(16, 8, PIN, NEO_MATRIX_BOTTOM + NEO_MATRIX_LEFT + NEO_MATRIX_COLUMNS, NEO_GRB + NEO_KHZ800);
Adafruit_NeoMatrix matrix2 = Adafruit_NeoMatrix(16, 8, PIN, NEO_MATRIX_BOTTOM + NEO_MATRIX_LEFT + NEO_MATRIX_COLUMNS, NEO_GRB + NEO_KHZ800);
Adafruit_NeoMatrix matrix3 = Adafruit_NeoMatrix(16, 8, PIN, NEO_MATRIX_BOTTOM + NEO_MATRIX_LEFT + NEO_MATRIX_COLUMNS, NEO_GRB + NEO_KHZ800);
const uint16_t colors[] = { matrix.Color(255, 0, 0), matrix.Color(0, 255, 0), matrix.Color(0, 0, 255), matrix.Color(204, 0, 204), matrix.Color(204, 204, 0), matrix.Color(0, 255, 255), matrix.Color(255, 10, 127), matrix.Color(127, 0, 255), matrix.Color(255, 99, 255) };

int tension=0;
int increment=0;
int x = matrix.width();
int pass = 0;
int B = 0;
String test;

const char* ssid = "iPhone de Pauline";
const char* password = "Pauline71480";

bool isScrolling = false;
String scrollWord = "";

AsyncWebServer server(80);

////////////// FONCTION ////////////////////////
void notFound(AsyncWebServerRequest *request) {
  request->send(404, "text/plain", "Not found");
}

void colorWipe(uint32_t c, uint8_t wait) {
  for(uint16_t i=0; i<128; i++) {
    matrix.setPixelColor(i, c);
    matrix.show();
    delay(wait);
  }
}

void tensionn(){
 tension=(analogRead(34))/5;
  B=map(tension,0,4096,3,819);
  matrix.setBrightness(B);
  delay(20);
     if(tension==END_V){
      increment=-1;
    }
    matrix.show();
 delay(20);
}

void defilement() {
  if (!isScrolling) return;

  matrix.fillScreen(0);
  matrix.setCursor(x, 0);
  matrix.print(scrollWord);

  if (--x < -((int)scrollWord.length() * 6)) {
    x = matrix.width();
    if (++pass >= 9)
      pass = 0;
    matrix.setTextColor(colors[pass]);
  }
  matrix.show();
  delay(100);
}

void displayWord(String word) {
  matrix.fillScreen(0);
  matrix.setCursor(0, 0);
  matrix.print(word);
  matrix.show();
}

void displayWordAndColor(String word, uint32_t color) {
  isScrolling = true;
  scrollWord = word;
  x = matrix.width();
  matrix.fillScreen(0);
  matrix.setTextColor(color);
  matrix.setCursor(0, 0);
  matrix.print(word);
  matrix.show();
}

void hexToRGB(String hexValue, uint8_t &r, uint8_t &g, uint8_t &b) {
    // Assurez-vous que la chaîne contient 7 caractères (y compris le '#')
    if (hexValue.length() == 7 && hexValue[0] == '#') {
        // Extraire les composantes de couleur rouge (R), verte (G) et bleue (B)
        r = strtol(hexValue.substring(1, 3).c_str(), NULL, 16);
        g = strtol(hexValue.substring(3, 5).c_str(), NULL, 16);
        b = strtol(hexValue.substring(5, 7).c_str(), NULL, 16);
    } else {
        // En cas d'erreur, définissez les valeurs par défaut sur 0
        r = g = b = 0;
    }
}

void displayMode1() {
    // Initialiser chaque matrice de LED avec sa propre configuration
    matrix1.begin();
    matrix2.begin();
    matrix3.begin();

    // Définir les couleurs pour chaque matrice
    matrix1.fillScreen(matrix1.Color(0, 0, 255)); // Bleu
    matrix2.fillScreen(matrix2.Color(255, 255, 255)); // Blanc
    matrix3.fillScreen(matrix3.Color(255, 0, 0)); // Rouge

    // Afficher les couleurs sur toutes les matrices simultanément
    matrix1.show();
    matrix2.show();
    matrix3.show();

    delay(1000);
}

void displayMode2() {
  while(true){
    // Afficher la première matrice en bleu
    matrix.fillScreen(matrix.Color(0, 0, 255)); // Bleu
    matrix.show();
    delay(500);

    // Afficher la troisième matrice en rouge
    matrix.fillScreen(matrix.Color(255, 0, 0)); // Rouge
    matrix.show();
    delay(500);
  }
}

void displayColor(uint32_t color) {
  matrix.fillScreen(color);
  matrix.show();
}

////////////// SETUP ////////////////////////
void setup() {
  tension=STARTV;
  increment=1;

  pinMode(LED_PIN, OUTPUT);
  digitalWrite(LED_PIN, LOW);
  pinMode(PIN, OUTPUT);
  digitalWrite(PIN, LOW);
  matrix.begin();
  matrix.setTextWrap(false);
  matrix.setBrightness(40);
  Serial.begin(115200);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connexion au WiFi en cours...");
  }

  Serial.println();
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());

  server.on("/led", HTTP_GET, [](AsyncWebServerRequest *request) {
    if (request->hasParam("action")) {
      String action = request->getParam("action")->value();
      if (action == "on") {
        int value = 128;
        value = map(value, 0, 800, 0, 255);
        analogWrite(LED_PIN, value);
        // Allumer la matrice LED couleur blanche
        matrix.fillScreen(matrix.Color(255, 255, 255));
        matrix.show();
        request->send(200, "text/plain", "LED allumée");
      } else if (action == "off") {
        // Éteindre la LED
        analogWrite(LED_PIN, 0);
        // Éteindre la matrice LED
        matrix.fillScreen(matrix.Color(0, 0, 0));
        matrix.show();
        request->send(200, "text/plain", "LED éteinte");
      } else {
        request->send(400, "text/plain", "Commande invalide");
      }
    } else {
      request->send(400, "text/plain", "Paramètre 'action' manquant");
    }
  });

  server.on("/display_word_and_color", HTTP_GET, [](AsyncWebServerRequest *request) {
    if (request->hasParam("word") && request->hasParam("color")) {
      String word = request->getParam("word")->value();
      String colorValue = request->getParam("color")->value();
      // Afficher les valeurs du mot et de la couleur dans le moniteur série
      Serial.print("Mot : ");
      Serial.println(word);
      Serial.print("Couleur : ");
      Serial.println(colorValue);
      // Convertir la couleur hexadécimale en valeur entière
      uint8_t r, g, b;
      hexToRGB(colorValue, r, g, b);
      Serial.print("R : ");
      Serial.println(r);
      Serial.print("G : ");
      Serial.println(g);
      Serial.print("B : ");
      Serial.println(b);
      // Afficher le mot sur la matrice LED avec la couleur sélectionnée
      displayWordAndColor(word, matrix.Color(r, g, b));
      // Envoyer une réponse HTTP pour indiquer que le mot a été affiché avec succès
      request->send(200, "text/plain", "Mot affiché avec succès sur les matrices de LED");
    } else {
      // Envoyer une réponse HTTP indiquant que les paramètres requis sont manquants
      request->send(400, "text/plain", "Paramètres 'word' ou 'color' manquants");
    }
  });

  server.on("/color", HTTP_GET, [](AsyncWebServerRequest *request) {
    if (request->hasParam("color")) {
      String colorValue = request->getParam("color")->value();
      Serial.println("colorValue :");
      Serial.println(colorValue);

      uint8_t r, g, b;
      hexToRGB(colorValue, r, g, b);
      Serial.print("R : ");
      Serial.println(r);
      Serial.print("G : ");
      Serial.println(g);
      Serial.print("B : ");
      Serial.println(b);

      displayColor(matrix.Color(r, g, b));

      request->send(200, "text/plain", "LED allumées avec succès");
    } else {
      request->send(400, "text/plain", "Paramètre 'color' manquant");
    }
  });

  server.on("/mode", HTTP_GET, [](AsyncWebServerRequest *request) {
    if (request->hasParam("mode")) {
      int mode = request->getParam("mode")->value().toInt();
      if (mode == 1) {
        Serial.print("Mode 1 ");
        displayMode1();
        request->send(200, "text/plain", "Mode 1 activé");
      } else if (mode == 2) {
        Serial.print("Mode 2 ");
        displayMode2();
        request->send(200, "text/plain", "Mode 2 activé");
      } else {
        request->send(400, "text/plain", "Mode invalide");
      }
    } else {
      request->send(400, "text/plain", "Paramètre 'mode' manquant");
    }
  });

  server.onNotFound(notFound);
  server.begin();
}

////////////// LOOP ////////////////////////
void loop() {
  tensionn();
  defilement();
}

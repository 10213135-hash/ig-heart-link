<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>粒子愛心 (最終穩定版)</title>
    
    <style>
        /* 頁面基礎設定 */
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #000; /* 黑色背景 */
            overflow: hidden;
        }
    </style>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.0/p5.js"></script>
    
    <script>
        // --- 設定變數 ---
        let particles = [];
        const NUM_PARTICLES = 4000; // 粒子數量
        const HEART_COLOR = '#ff69b4'; // 粉紅色
        let currentScale = 1.0;
        let animationComplete = false;
        let startTime;
        let scaleFactor; 

        // --- p5.js 初始化設定 ---
        function setup() {
            let canvas = createCanvas(windowWidth, windowHeight);
            canvas.style('display', 'block');
            background(0); 
            
            // 根據最小邊長計算縮放比例 (調整讓愛心垂直且夠大)
            scaleFactor = min(width, height) * 0.25; 
            
            for (let i = 0; i < NUM_PARTICLES; i++) {
                particles.push(new Particle());
            }
            
            startTime = millis(); 
        }

        // --- p5.js 繪圖迴圈 ---
        function draw() {
            // 繪製半透明黑色，造成粒子殘影效果
            fill(0, 15); 
            rect(0, 0, width, height);

            // 處理單次跳動動畫
            if (!animationComplete) {
                let elapsed = (millis() - startTime) / 1000; 
                const DURATION = 2.5; 

                if (elapsed < DURATION) {
                    let t = map(elapsed, 0, DURATION, 0, 1);
                    currentScale = 1.0 + (0.15 * sin(t * PI)); 
                } else {
                    currentScale = 1.0; 
                    animationComplete = true; 
                }
            }
            
            translate(width / 2, height / 2);
            
            for (let p of particles) {
                p.update();
                p.display();
            }
        }

        // --- 粒子物件定義 ---
        class Particle {
            constructor() {
                this.t = random(0, TWO_PI); 
                this.currentPos = createVector(random(-50, 50), random(-50, 50)); 
                this.color = HEART_COLOR;
                this.size = random(1.5, 3); // 粒子大小
                this.vel = createVector(0, 0);
                this.acc = createVector(0, 0);
            }

            // *** 關鍵：使用穩定且常見的心形線公式 ***
            getHeartPosition(t) {
                // x(t) = 16 sin^3(t)
                let x = 16 * pow(sin(t), 3);
                // y(t) = 13 cos(t) - 5 cos(2t) - 2 cos(3t) - cos(4t)
                let y = 13 * cos(t) - 5 * cos(2 * t) - 2 * cos(3 * t) - cos(4 * t);
                
                // 應用縮放因子
                return createVector(x * scaleFactor / 16, -y * scaleFactor / 16); 
            }

            update() {
                let target = this.getHeartPosition(this.t);
                
                target.mult(currentScale); 

                let steering = p5.Vector.sub(target, this.currentPos);
                this.acc.add(steering.mult(0.005)); 

                this.vel.add(this.acc);
                this.vel.limit(5); 
                this.currentPos.add(this.vel);
                this.acc.mult(0); 
                
                // 讓粒子在心形線上緩慢流動
                this.t += 0.00005;
                if (this.t > TWO_PI) this.t = 0;
            }

            display() {
                noStroke();
                fill(this.color);
                ellipse(this.currentPos.x, this.currentPos.y, this.size); 
            }
        }

        function windowResized() {
            resizeCanvas(windowWidth, windowHeight);
            scaleFactor = min(width, height) * 0.25;
        }
    </script>
</head>
<body>
</body>
</html>
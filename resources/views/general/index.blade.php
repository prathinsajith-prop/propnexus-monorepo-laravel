<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lavalite UI Builder - Fluent Interface Design for Laravel</title>
    <link rel="icon" type="image/png" href="assets/image/lavalite-logo.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:wght@300;400;500;600&family=Google+Sans+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined" rel="stylesheet">

    <style>
        :root {
            --bg-color: #ffffff;
            --surface-color: #f1f3f4;
            --text-primary: #202124;
            --text-secondary: #5f6368;
            --accent-blue: #1a73e8;
            /* Darker blue for light mode */
            --button-primary-bg: #1a73e8;
            --button-primary-text: #ffffff;
            --border-color: #dadce0;

            /* Gradient for blobs */
            --blob-gradient-1: linear-gradient(135deg, #e8f0fe 0%, #d2e3fc 100%);
            --blob-gradient-2: linear-gradient(135deg, #fce8e6 0%, #fad2cf 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Google Sans Flex', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }

        /* Abstract Background Blobs (Animation) */
        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            filter: blur(80px);
            opacity: 0.6;
            border-radius: 50%;
            z-index: -1;
            animation: move 25s infinite alternate ease-in-out;
        }

        .blob-1 {
            top: -20%;
            left: -10%;
            background: var(--blob-gradient-1);
        }

        .blob-2 {
            bottom: -20%;
            right: -10%;
            background: var(--blob-gradient-2);
            animation-delay: -10s;
        }

        @keyframes move {
            from {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }

            to {
                transform: translate(50px, 50px) scale(1.1) rotate(10deg);
            }
        }

        /* Navigation */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 64px;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.85);
            /* Light frosted glass */
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            font-size: 22px;
            font-weight: 500;
            letter-spacing: -0.5px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .brand-logo img {
            height: 24px;
            width: auto;
        }

        .brand-logo span {
            color: var(--text-secondary);
            /* Grey "UI Builder" */
        }

        .nav-right {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--text-primary);
        }

        .btn-sign-in {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--accent-blue);
            padding: 10px 24px;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-sign-in:hover {
            background: #f1f3f4;
        }

        /* Hero Section */
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 180px 20px 80px;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 32px;
            color: var(--accent-blue);
            font-family: 'Google Sans Mono', monospace;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 72px;
            line-height: 1.1;
            font-weight: 600;
            letter-spacing: -2px;
            max-width: 900px;
            margin-bottom: 32px;
            color: var(--text-primary);
        }

        p.hero-sub {
            font-size: 20px;
            color: var(--text-secondary);
            max-width: 640px;
            line-height: 1.6;
            margin-bottom: 48px;
        }

        .cta-group {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .btn-primary {
            background-color: var(--button-primary-bg);
            color: var(--button-primary-text);
            padding: 14px 32px;
            border-radius: 32px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(26, 115, 232, 0.35);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            padding: 14px 32px;
            border-radius: 32px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        /* Abstract UI Visualization (The "IDE" preview) */
        .ide-preview {
            margin-top: 80px;
            width: 90%;
            max-width: 1200px;
            height: 600px;
            background: #ffffff;
            border-radius: 24px;
            border: 5px solid #ffffff;
            /* Inner white border */
            box-shadow:
                0 0 0 1px rgba(0, 0, 0, 0.05),
                /* Thin border */
                0 25px 50px -12px rgba(0, 0, 0, 0.15),
                /* Drop shadow */
                0 0 0 6px rgba(255, 255, 255, 0.5);
            /* Outer glow ring */
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: 280px 1fr 320px;
            grid-template-rows: 56px 1fr;
        }

        .ide-header {
            grid-column: 1 / -1;
            border-bottom: 1px solid var(--border-color);
            background: #f8f9fa;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
        }

        .window-controls {
            display: flex;
            gap: 8px;
        }

        .control {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #444746;
        }

        .control.red {
            background: #ff5f57;
        }

        .control.yellow {
            background: #febc2e;
        }

        .control.green {
            background: #28c840;
        }

        .sidebar {
            border-right: 1px solid var(--border-color);
            padding: 20px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 8px;
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .activity-item.active {
            background: #e8f0fe;
            /* Light blue background for active item */
            color: var(--accent-blue);
        }

        .main-editor {
            padding: 40px;
            font-family: 'Google Sans Mono', monospace;
            color: var(--text-primary);
            /* Darker text for main editor */
            font-size: 14px;
            line-height: 1.6;
        }

        .code-line {
            display: block;
        }

        .keyword {
            color: #d93025;
            /* Red for keywords */
        }

        .string {
            color: #188038;
            /* Green for strings */
        }

        .function {
            color: #1a73e8;
            /* Blue for functions */
        }

        /* Floating particles (Subtle floating effect) */
        .particle {
            position: absolute;
            background: radial-gradient(circle, var(--accent-blue) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0.1;
            pointer-events: none;
            animation: float 10s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-20px) scale(1.1);
            }
        }

        /* Visual Enhancements */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .text-gradient {
            background: linear-gradient(135deg, #1a73e8 0%, #a855f7 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: rgba(26, 115, 232, 0.1);
            color: var(--accent-blue);
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Typing Animation for Code */
        .typing-effect {
            overflow: hidden;
            white-space: nowrap;
            border-right: 2px solid var(--accent-blue);
            width: 0;
            animation: typing 3s steps(40, end) forwards, blink 1s step-end infinite;
            display: inline-block;
            vertical-align: bottom;
        }

        @keyframes typing {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }

        @keyframes blink {

            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: var(--accent-blue)
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 42px;
            }

            .nav-right {
                display: none;
            }

            nav {
                padding: 20px;
            }

            .ide-preview {
                display: none;
            }
        }
    </style>
</head>

<body>

    <nav class="glass-panel" style="background: rgba(255, 255, 255, 0.8);">
        <div class="nav-left">
            <a href="#" class="brand-logo">
                Lavalite <span>Ui Builder</span>
            </a>
        </div>
        <div class="nav-right">
            <a href="https://litepie.com/docs" target="_blank" class="nav-link">Documentation</a>
            <a href="https://github.com/litepie" target="_blank" class="nav-link">GitHub</a>
            <a href="/login" class="btn-sign-in">Sign in</a>
        </div>
    </nav>

    <div class="hero">
        <div class="hero-badge">
            <span class="material-symbols-outlined" style="font-size: 16px;">terminal</span>
            Lavalite UI Builder v1.0
        </div>
        <h1><span class="text-gradient">Fluent Interface Design</span><br>for Modern Laravel Apps.</h1>
        <p class="hero-sub">
            The ultimate layout and form builder for Laravel.
            Define complex administrative interfaces, data tables, and forms using a simple, chainable PHP API.
        </p>

        <!-- Feature Badges -->
        <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 32px;">
            <span class="feature-badge"><span class="material-symbols-outlined" style="font-size: 14px;">bolt</span>Fluency</span>
            <span class="feature-badge"><span class="material-symbols-outlined" style="font-size: 14px;">palette</span>Theming</span>
            <span class="feature-badge"><span class="material-symbols-outlined" style="font-size: 14px;">widgets</span>Components</span>
        </div>

        <div class="cta-group">
            <a href="/dashboard" class="btn-primary">View Demo</a>
            <a href="https://github.com/litepie/laravel" target="_blank" class="btn-secondary">
                <span class="material-symbols-outlined">code</span>
                Documentation
            </a>
        </div>

        <div class="ide-preview glass-panel" style="margin-top: 60px;">
            <!-- Subtle floating glow behind -->
            <div class="particle" style="top: 20%; left: 30%; width: 300px; height: 300px; animation-delay: 0s;"></div>
            <div class="particle" style="bottom: 10%; right: 20%; width: 400px; height: 400px; animation-delay: -5s;"></div>

            <div class="ide-header">
                <div class="window-controls">
                    <div class="control red"></div>
                    <div class="control yellow"></div>
                    <div class="control green"></div>
                </div>
                <div style="font-size: 12px; color: #9aa0a6;">GeneralController.php — Lavalite</div>
            </div>

            <div class="sidebar">
                <div class="activity-item active">
                    <span class="material-symbols-outlined" style="font-size: 18px;">view_quilt</span>
                    <span>Layout Builder</span>
                </div>
                <div class="activity-item">
                    <span class="material-symbols-outlined" style="font-size: 18px;">wysiwyg</span>
                    <span>Form Factory</span>
                </div>
                <div class="activity-item">
                    <span class="material-symbols-outlined" style="font-size: 18px;">dataset</span>
                    <span>Data Grid</span>
                </div>
            </div>

            <div class="main-editor">
                <span class="code-line"><span class="keyword">use</span> Litepie\Form\Facades\Form;</span>
                <br>
                <div class="typing-container">
                    <span class="code-line"><span class="keyword">return</span> Form::<span class="function">create</span>(<span class="string">'user_profile'</span>)</span>
                    <span class="code-line">&nbsp;&nbsp;-><span class="function">section</span>(<span class="string">'details'</span>, <span class="keyword">function</span> ($f) {</span>
                    <span class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;$f-><span class="function">text</span>(<span class="string">'name'</span>)-><span class="function">required</span>();</span>
                    <span class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;$f-><span class="function">email</span>(<span class="string">'contact_email'</span>);</span>
                    <span class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;$f-><span class="function">file</span>(<span class="string">'avatar'</span>)-><span class="function">accept</span>(<span class="string">'image/*'</span>);</span>
                    <span class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;$f-><span class="function">rating</span>(<span class="string">'skills'</span>)-><span class="function">stars</span>(5);</span>
                    <span class="code-line">&nbsp;&nbsp;&nbsp;&nbsp;$f-><span class="function">range</span>(<span class="string">'experience'</span>)-><span class="function">min</span>(1)-><span class="function">max</span>(10);</span>
                    <span class="code-line">&nbsp;&nbsp;})<span class="typing-effect">->render();</span></span>
                </div>
            </div>

            <div class="sidebar" style="border-left: 1px solid var(--border-color); padding: 20px;">
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #5f6368; margin-bottom: 20px;">Component Toolbox</div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <!-- Input Field -->
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 28px; height: 28px; background: #cbedd5; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #000;"><span class="material-symbols-outlined" style="font-size: 16px;">edit</span></div>
                        <div style="font-size: 13px; color: var(--text-primary);">Input / Text</div>
                    </div>

                    <!-- File Upload -->
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 28px; height: 28px; background: #d3e3fd; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #000;"><span class="material-symbols-outlined" style="font-size: 16px;">upload_file</span></div>
                        <div style="font-size: 13px; color: var(--text-primary);">File Upload</div>
                    </div>

                    <!-- Date Picker -->
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 28px; height: 28px; background: #fce8e6; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #000;"><span class="material-symbols-outlined" style="font-size: 16px;">calendar_today</span></div>
                        <div style="font-size: 13px; color: var(--text-primary);">Date Picker</div>
                    </div>

                    <!-- Rich Editor -->
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 28px; height: 28px; background: #feefc3; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #000;"><span class="material-symbols-outlined" style="font-size: 16px;">format_bold</span></div>
                        <div style="font-size: 13px; color: var(--text-primary);">Rich Text</div>
                    </div>

                    <!-- Rating -->
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 28px; height: 28px; background: #f1f3f4; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #000;"><span class="material-symbols-outlined" style="font-size: 16px;">star</span></div>
                        <div style="font-size: 13px; color: var(--text-primary);">Rating Stars</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
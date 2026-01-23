<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Laravel Lite</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 900px;
            width: 100%;
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }
        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .endpoints {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .endpoint {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .endpoint-method {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85em;
            margin-right: 10px;
        }
        .get { background: #10b981; color: white; }
        .post { background: #3b82f6; color: white; }
        .put { background: #f59e0b; color: white; }
        .delete { background: #ef4444; color: white; }
        .endpoint-path {
            font-family: 'Courier New', monospace;
            color: #333;
        }
        .endpoint-desc {
            color: #666;
            font-size: 0.9em;
            margin-top: 8px;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .feature {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 3px solid #764ba2;
        }
        .feature-title {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .feature-desc {
            color: #666;
            font-size: 0.9em;
        }
        .note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Blog Management System</h1>
        <p class="subtitle">Comprehensive blog platform with advanced features</p>

        <div class="stats" id="stats">
            <div class="stat-card">
                <div class="stat-value" id="total-posts">-</div>
                <div class="stat-label">Total Posts</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="published">-</div>
                <div class="stat-label">Published</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="drafts">-</div>
                <div class="stat-label">Drafts</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="views">-</div>
                <div class="stat-label">Total Views</div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">🎯 Key Features</h2>
            <div class="features">
                <div class="feature">
                    <div class="feature-title">SEO Optimized</div>
                    <div class="feature-desc">Meta tags, keywords, schema markup</div>
                </div>
                <div class="feature">
                    <div class="feature-title">Multi-Language</div>
                    <div class="feature-desc">Support for multiple languages</div>
                </div>
                <div class="feature">
                    <div class="feature-title">Content Scheduling</div>
                    <div class="feature-desc">Schedule posts for future publication</div>
                </div>
                <div class="feature">
                    <div class="feature-title">Rich Media</div>
                    <div class="feature-desc">Images, galleries, videos, attachments</div>
                </div>
                <div class="feature">
                    <div class="feature-title">Analytics</div>
                    <div class="feature-desc">Track views, likes, shares, comments</div>
                </div>
                <div class="feature">
                    <div class="feature-title">Actions Pattern</div>
                    <div class="feature-desc">Clean architecture with Litepie Actions</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">🚀 API Endpoints</h2>
            <div class="endpoints">
                <div class="endpoint">
                    <span class="endpoint-method get">GET</span>
                    <span class="endpoint-path">/api/blogs</span>
                    <div class="endpoint-desc">List all blogs with filtering, sorting, and pagination</div>
                </div>
                <div class="endpoint">
                    <span class="endpoint-method post">POST</span>
                    <span class="endpoint-path">/api/blogs</span>
                    <div class="endpoint-desc">Create a new blog post</div>
                </div>
                <div class="endpoint">
                    <span class="endpoint-method get">GET</span>
                    <span class="endpoint-path">/api/blogs/{id}</span>
                    <div class="endpoint-desc">Get a specific blog post</div>
                </div>
                <div class="endpoint">
                    <span class="endpoint-method put">PUT</span>
                    <span class="endpoint-path">/api/blogs/{id}</span>
                    <div class="endpoint-desc">Update a blog post</div>
                </div>
                <div class="endpoint">
                    <span class="endpoint-method delete">DELETE</span>
                    <span class="endpoint-path">/api/blogs/{id}</span>
                    <div class="endpoint-desc">Delete a blog post (supports soft delete)</div>
                </div>
                <div class="endpoint">
                    <span class="endpoint-method get">GET</span>
                    <span class="endpoint-path">/api/blogs/stats</span>
                    <div class="endpoint-desc">Get blog statistics</div>
                </div>
                <div class="endpoint">
                    <span class="endpoint-method get">GET</span>
                    <span class="endpoint-path">/blogs/layout</span>
                    <div class="endpoint-desc">Get complete blog layout configuration</div>
                </div>
            </div>
        </div>

        <div class="note">
            <strong>📌 Note:</strong> This blog system uses the Litepie Actions pattern for clean, maintainable code architecture. All business logic is separated into dedicated action classes.
        </div>
    </div>

    <script>
        // Load statistics
        fetch('/api/blogs/stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('total-posts').textContent = data.data.total;
                    document.getElementById('published').textContent = data.data.published;
                    document.getElementById('drafts').textContent = data.data.drafts;
                    document.getElementById('views').textContent = data.data.total_views.toLocaleString();
                }
            })
            .catch(error => console.error('Error loading stats:', error));
    </script>
</body>
</html>

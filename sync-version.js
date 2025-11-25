#!/usr/bin/env node

/**
 * Syncs version number between package.json and WordPress plugin header
 * Usage: node sync-version.js
 */

import { readFileSync, writeFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Read package.json
const packagePath = join(__dirname, 'package.json');
const packageJson = JSON.parse(readFileSync(packagePath, 'utf8'));
const version = packageJson.version;

console.log(`📦 Package version: ${version}`);

// Update PHP plugin file
const pluginFile = join(__dirname, 'enspyred-cookie-consent.php');
let phpContent = readFileSync(pluginFile, 'utf8');

// Replace version in WordPress plugin header
const versionRegex = /^Version:\s*(.+)$/m;
const match = phpContent.match(versionRegex);

if (match) {
  const oldVersion = match[1].trim();
  if (oldVersion !== version) {
    phpContent = phpContent.replace(versionRegex, `Version: ${version}`);
    writeFileSync(pluginFile, phpContent);
    console.log(`✅ Updated plugin header: ${oldVersion} → ${version}`);
  } else {
    console.log(`✅ Plugin header already at version ${version}`);
  }
} else {
  console.error('❌ Could not find Version line in plugin file');
  process.exit(1);
}

// Update readme.txt if it exists
const readmePath = join(__dirname, 'readme.txt');
try {
  let readmeContent = readFileSync(readmePath, 'utf8');
  const stableTagRegex = /^Stable tag:\s*(.+)$/m;
  const readmeMatch = readmeContent.match(stableTagRegex);

  if (readmeMatch) {
    const oldStableTag = readmeMatch[1].trim();
    if (oldStableTag !== version) {
      readmeContent = readmeContent.replace(stableTagRegex, `Stable tag: ${version}`);
      writeFileSync(readmePath, readmeContent);
      console.log(`✅ Updated readme.txt stable tag: ${oldStableTag} → ${version}`);
    } else {
      console.log(`✅ readme.txt stable tag already at version ${version}`);
    }
  }
} catch (err) {
  console.log('ℹ️  readme.txt not found (will be created later)');
}

console.log('✨ Version sync complete!');

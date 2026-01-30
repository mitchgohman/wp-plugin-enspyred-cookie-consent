#!/bin/bash

##############################################################################
# Release Script for Enspyred Cookie Consent Plugin
#
# Usage: ./scripts/release.sh [major|minor|patch]
#
# This script automates the complete release workflow:
# 1. Bumps version in package.json
# 2. Syncs version to plugin PHP file
# 3. Builds and packages clean distribution
# 4. Creates git commit and tag
# 5. Pushes to GitHub
#
# After running, manually create GitHub release and attach the ZIP file.
##############################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default to patch if no argument provided
VERSION_TYPE=${1:-patch}

# Validate version type
if [[ ! "$VERSION_TYPE" =~ ^(major|minor|patch)$ ]]; then
  echo -e "${RED}❌ Invalid version type: $VERSION_TYPE${NC}"
  echo -e "${YELLOW}Usage: ./scripts/release.sh [major|minor|patch]${NC}"
  exit 1
fi

echo -e "${BLUE}🚀 Starting release process...${NC}\n"

# Get current version
CURRENT_VERSION=$(node -p "require('./package.json').version")
echo -e "${BLUE}📦 Current version: ${CURRENT_VERSION}${NC}"

# Check for uncommitted changes
if [[ -n $(git status -s) ]]; then
  echo -e "${YELLOW}⚠️  Warning: You have uncommitted changes.${NC}"
  read -p "Continue anyway? (y/N) " -n 1 -r
  echo
  if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}❌ Release cancelled.${NC}"
    exit 1
  fi
fi

# Step 1: Bump version in package.json
echo -e "\n${BLUE}Step 1: Bumping version (${VERSION_TYPE})...${NC}"
npm version $VERSION_TYPE --no-git-tag-version

# Get new version
NEW_VERSION=$(node -p "require('./package.json').version")
echo -e "${GREEN}✓ Version bumped: ${CURRENT_VERSION} → ${NEW_VERSION}${NC}"

# Step 2: Sync version to plugin PHP file
echo -e "\n${BLUE}Step 2: Syncing version to plugin file...${NC}"
npm run sync-version

# Step 3: Build and package
echo -e "\n${BLUE}Step 3: Building and packaging plugin...${NC}"
npm run package
echo -e "${GREEN}✓ Clean distribution created in ./dist/enspyred-cookie-consent/${NC}"
echo -e "${GREEN}✓ Ready for ZIP creation${NC}"

# Step 4: Create ZIP file
echo -e "\n${BLUE}Step 4: Creating ZIP file...${NC}"
cd ./dist
zip -r enspyred-cookie-consent.zip enspyred-cookie-consent -x "*.DS_Store"
cd - > /dev/null
echo -e "${GREEN}✓ ZIP file created: ./dist/enspyred-cookie-consent.zip${NC}"

# Step 5: Git commit and tag
echo -e "\n${BLUE}Step 5: Creating git commit and tag...${NC}"
git add .
git commit -m "Release v${NEW_VERSION}"
git tag -a "v${NEW_VERSION}" -m "Version ${NEW_VERSION}"
echo -e "${GREEN}✓ Git commit and tag created${NC}"

# Step 6: Push to GitHub
echo -e "\n${BLUE}Step 6: Pushing to GitHub...${NC}"
read -p "Push to GitHub now? (Y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Nn]$ ]]; then
  echo -e "${YELLOW}⚠️  Skipping push. Remember to push manually:${NC}"
  echo -e "${YELLOW}   git push origin main${NC}"
  echo -e "${YELLOW}   git push origin v${NEW_VERSION}${NC}"
else
  git push origin main
  git push origin "v${NEW_VERSION}"
  echo -e "${GREEN}✓ Pushed to GitHub${NC}"
fi

# Success summary
echo -e "\n${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✨ Release v${NEW_VERSION} complete!${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"

echo -e "${YELLOW}📋 Next steps:${NC}"
echo -e "   1. Go to: ${BLUE}https://github.com/enspyred/wp-plugin-enspyred-cookie-consent/releases/new${NC}"
echo -e "   2. Select tag: ${BLUE}v${NEW_VERSION}${NC}"
echo -e "   3. Title: ${BLUE}Version ${NEW_VERSION}${NC}"
echo -e "   4. Attach file: ${BLUE}dist/enspyred-cookie-consent.zip${NC}"
echo -e "   5. Publish release\n"

echo -e "${GREEN}Distribution ZIP location:${NC}"
echo -e "   $(cd ./dist && pwd)/enspyred-cookie-consent.zip\n"

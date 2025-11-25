/* global __PLUGIN_VERSION__ */
import React, { useState } from "react";
import styled, { keyframes } from "styled-components";
import { setConsent } from "./core/consent";
import { blockAll } from "./core/trackingBlocker";

const slideUp = keyframes`
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
`;

const slideDown = keyframes`
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(100%);
        opacity: 0;
    }
`;

const BannerContainer = styled.div`
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    background: white;
    border-top: 3px solid #e0e0e0;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    animation: ${(props) => (props.$isClosing ? slideDown : slideUp)} 0.3s
        ease-out forwards;
`;

const BannerContent = styled.div`
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;

    @media (max-width: 768px) {
        padding: 20px 16px;
    }
`;

const CloseButton = styled.button`
    position: absolute;
    top: 16px;
    right: 16px;
    background: none;
    border: none;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    color: #666;
    padding: 4px 8px;
    transition: color 0.2s;

    &:hover {
        color: #333;
    }

    @media (max-width: 768px) {
        top: 12px;
        right: 12px;
    }
`;

const Heading = styled.h2`
    margin: 0 40px 12px 0;
    font-size: 20px;
    font-weight: 600;
    color: #333;

    @media (max-width: 768px) {
        font-size: 18px;
        margin-right: 30px;
    }
`;

const Description = styled.p`
    margin: 0 0 16px 0;
    font-size: 14px;
    line-height: 1.6;
    color: #666;

    @media (max-width: 768px) {
        font-size: 13px;
    }
`;

const PolicyLink = styled.a`
    color: #0073aa;
    text-decoration: underline;
    display: inline-block;
    font-size: 14px;

    &:hover {
        color: #005177;
    }
`;

const ButtonContainer = styled.div`
    flex: 0 0 350px;

    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;

    @media (max-width: 768px) {
        flex-direction: column;
    }
`;

const Button = styled.button`
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    white-space: nowrap;

    @media (max-width: 768px) {
        width: 100%;
        padding: 14px 20px;
    }
`;

const AcceptButton = styled(Button)`
    background: #0073aa;
    color: white;

    &:hover {
        background: #005177;
    }
`;

const OptOutButton = styled(Button)`
    background: white;
    color: #333;
    border: 2px solid #ddd;

    &:hover {
        border-color: #999;
        background: #f5f5f5;
    }
`;

const ContentWrapper = styled.div`
    display: flex;
    gap: 18px;
`;
const TextWrapper = styled.div`
    flex: 1;
`;

const CookieConsentBanner = ({ settings }) => {
    const [isClosing, setIsClosing] = useState(false);
    const [isHidden, setIsHidden] = useState(false);

    const {
        banner_heading,
        banner_text,
        cookie_policy_url,
        cookie_policy_text,
        cookie_policy_new_window,
        privacy_policy_url,
        privacy_policy_text,
        privacy_policy_new_window,
        accept_button_text,
        opt_out_button_text,
    } = settings;

    // Hide component by returning null
    if (isHidden) {
        return null;
    }

    const handleClose = () => {
        setIsClosing(true);
        setConsent("dismissed"); // Treat close as implicit acceptance
        setTimeout(() => {
            setIsHidden(true); // Hide component
        }, 300);
    };

    const handleAccept = () => {
        setIsClosing(true);
        setConsent("accepted");
        setTimeout(() => {
            setIsHidden(true); // Hide component
        }, 300);
    };

    const handleOptOut = () => {
        setIsClosing(true);
        blockAll(); // Block tracking IMMEDIATELY
        setConsent("opted-out");
        setTimeout(() => {
            setIsHidden(true); // Hide component
        }, 300);
    };

    return (
        <BannerContainer
            $isClosing={isClosing}
            data-version={__PLUGIN_VERSION__}
        >
            <BannerContent>
                <CloseButton onClick={handleClose} aria-label="Close">
                    ×
                </CloseButton>

                <ContentWrapper>
                    <TextWrapper>
                        <Heading>
                            {banner_heading || "We value your privacy"}
                        </Heading>
                        <Description>
                            {banner_text ||
                                "This website processes personal data. You can opt out of the sale of your personal information."}
                            {(cookie_policy_url || privacy_policy_url) && (
                                <>
                                    {" "}
                                    {cookie_policy_url && (
                                        <PolicyLink
                                            href={cookie_policy_url}
                                            {...(cookie_policy_new_window && {
                                                target: "_blank",
                                                rel: "noopener noreferrer",
                                            })}
                                        >
                                            {cookie_policy_text ||
                                                "Cookie Policy"}
                                        </PolicyLink>
                                    )}
                                    {cookie_policy_url &&
                                        privacy_policy_url &&
                                        " | "}
                                    {privacy_policy_url && (
                                        <PolicyLink
                                            href={privacy_policy_url}
                                            {...(privacy_policy_new_window && {
                                                target: "_blank",
                                                rel: "noopener noreferrer",
                                            })}
                                        >
                                            {privacy_policy_text ||
                                                "Privacy Policy"}
                                        </PolicyLink>
                                    )}
                                </>
                            )}
                        </Description>
                    </TextWrapper>

                    <ButtonContainer>
                        <AcceptButton onClick={handleAccept}>
                            {accept_button_text || "Accept Cookies"}
                        </AcceptButton>

                        <OptOutButton onClick={handleOptOut}>
                            {opt_out_button_text || "Reject Cookies"}
                        </OptOutButton>
                    </ButtonContainer>
                </ContentWrapper>
            </BannerContent>
        </BannerContainer>
    );
};

export default CookieConsentBanner;

import React, {useState} from 'react';

export interface TooltipProps {
    message: string;
    children: React.ReactElement;
    position?: string;
}

/**
 * Renders tooltip around wrapped children.
 */
export default function Tooltip({message, position = 'top', children}: TooltipProps) {
    const [displayTooltip, setDisplayTooltip] = useState(false);

    /**
     * Hides tooltip.
     */
    function handleHideTooltip() {
        setDisplayTooltip(false);
    }

    /**
     * Shows tooltip.
     */
    function handleShowTooltip() {
        setDisplayTooltip(true);
    }

    return (
        <span className='anycomment-tooltip' onMouseLeave={handleHideTooltip}>
            {displayTooltip && (
                <div className={`anycomment-tooltip-bubble anycomment-tooltip-${position}`}>
                    <div className='anycomment-tooltip-message'>{message}</div>
                </div>
            )}
            <span className='anycomment-tooltip-trigger' onMouseOver={handleShowTooltip}>
                {children}
            </span>
        </span>
    );
}

Tooltip.displayName = 'Tooltip';

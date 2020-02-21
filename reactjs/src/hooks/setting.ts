import {useContext} from "react";
import AnyCommentContext from "~/components/AnyCommentContext";
import {
    SettingContextProps,
    ConfigurationOption,
    ContextValueProps,
    ConfigProps,
} from "~/components/AnyCommentProvider";

/**
 * Returns specific setting value.
 *
 * @param key
 * @param defaultValue
 */
export function useOption(key: keyof ConfigurationOption, defaultValue: any = undefined) {
    const context = useContext<ContextValueProps>(AnyCommentContext);

    if (!context || typeof context === 'object' && !context.settings.options) {
        return defaultValue;
    }

    const {options} = context.settings;

    return key in options ? options[key] : defaultValue;
}

/**
 * Get all available configuration options.
 */
export function useOptions(): ConfigurationOption {
    const contextValue = useContext<ContextValueProps>(AnyCommentContext);

    return contextValue && contextValue.settings.options;
}

export function useSettings(): SettingContextProps {
    const contextValue = useContext<ContextValueProps>(AnyCommentContext);

    return contextValue && contextValue.settings;
}

export function useConfig(): ConfigProps {
    const contextValue = useContext<ContextValueProps>(AnyCommentContext);

    return contextValue && contextValue.config;
}

export function getSettings(): SettingContextProps {
    // @ts-ignore
    return window.anyCommentApiSettings;
}
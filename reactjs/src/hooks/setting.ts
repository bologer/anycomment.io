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
    const context = useContext<ContextValueProps | undefined>(AnyCommentContext);

    if (!context || typeof context === 'object' && !context.settings.options) {
        return defaultValue;
    }

    const {options} = context.settings;

    return key in options ? options[key] : defaultValue;
}

/**
 * Get all available configuration options.
 */
export function useOptions(): ConfigurationOption | undefined {
    const contextValue = useContext<ContextValueProps | undefined>(AnyCommentContext);

    return contextValue && contextValue.settings.options;
}

export function useSettings(): SettingContextProps | undefined {
    const contextValue = useContext<ContextValueProps | undefined>(AnyCommentContext);

    return contextValue && contextValue.settings;
}

export function useConfig(): ConfigProps | undefined {
    const contextValue = useContext<ContextValueProps | undefined>(AnyCommentContext);

    return contextValue && contextValue.config;
}

export function getSettings(): SettingContextProps | undefined {
    // @ts-ignore
    return 'anyCommentApiSettings' in window ? window.anyCommentApiSettings : undefined;
}
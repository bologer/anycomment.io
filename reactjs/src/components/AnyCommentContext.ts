import React, {Context} from "react";
import {ContextValueProps} from "./AnyCommentProvider";

const AnyCommentContext: Context<ContextValueProps | undefined> = React.createContext<ContextValueProps | undefined>(undefined);

AnyCommentContext.displayName = 'AnyCommentContext';

export default AnyCommentContext;
